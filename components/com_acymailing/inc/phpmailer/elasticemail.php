<?php

acymailing_cmsLoaded();


/**
 * @copyright      Copyright (C) 2009-2023 ACYBA SAS - All rights reserved..
 * @license        GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
class acymailingElasticemail
{
    /**
     * Ressources : Connection to the elasticemail server
     */
    var $conn;

    /**
     * String : Last error...
     */
    var $error;
    var $Username = '';
    var $Password = '';
    var $poolName = '';

    /* Upload Function which uploads the file selected and return a part of the response.
     * The return value is the file's ID on ElasticEmail server.
     */
    private function uploadAttachment($filepath, $filename)
    {
        if (!empty ($this->attachment[$filepath])) {
            return $this->attachment[$filepath];
        }

        $data = file_get_contents($filepath);
        $header = 'PUT /v2/file/upload?apikey=' . urlencode($this->Password) . '&name=' . urlencode($filename) . " HTTP/1.0\r\n";
        $header .= "Host: api.elasticemail.com\r\n";
        $header .= "Connection: Keep-alive\r\n";
        $header .= "Content-Disposition: inline\r\n";
        $header .= 'Content-Length: ' . strlen($data) . "\r\n\r\n";
        $info = $header . $data;
        $result = $this->sendinfo($info);

        $this->attachment[$filepath] = $result['data']['fileid'];

        return $result['data']['fileid'];
    }

    /* Function which permit to send an email based on the object's values.
     * First, we do the test if we have enough credit to send emails.
     */
    function sendMail(&$object)
    {
        if (!$this->connect()) {
            return false;
        }

        $data = 'apikey=' . urlencode($this->Password);

        if ($object->attachment) {
            $ArrayID = [];
            foreach ($object->attachment as $oneAttachment) {
                $oneID = $this->uploadAttachment($oneAttachment[0], $oneAttachment[2]);
                if (!$oneID) {
                    return false;
                }
                $ArrayID[] = $oneID;
            }
            $data .= '&attachments=' . urlencode(implode(';', $ArrayID));
        }

        if (!empty($object->sendHTML) || !empty($object->AltBody)) {
            $data .= '&bodyHtml=' . urlencode($object->Body);
            if (!empty($object->AltBody)) {
                $data .= '&bodyText=' . urlencode($object->AltBody);
            }
        } else {
            $data .= '&bodyText=' . urlencode($object->Body);
        }

        if (!empty($object->mailid)) {
            $data .= '&channel=' . urlencode($object->mailid);
        }

        //We set only quoted printable as others may not work with DKIM
        if ($object->Encoding == 'quoted-printable') {
            $data .= '&encodingType=3';
        }

        if (!empty($object->From)) {
            $data .= '&from=' . urlencode($object->From);
        }
        if (!empty($object->FromName)) {
            $data .= '&fromName=' . urlencode($object->FromName);
        }

        //Do we have special headers?
        if (!empty($object->CustomHeader)) {
            $i = 1;
            $headers = [];
            foreach ($object->CustomHeader as $oneHeader) {
                $headers[] = 'headers_'.$i.'='.$oneHeader[0].': '.$oneHeader[1];
                $i ++;
            }
            $data .= '&headers='.urlencode(implode(',', $headers));
        }

        if (!empty($object->type) && strpos($object->type, 'notification') !== false) {
            $data .= '&isTransactional=true';
        }

        if (!empty($this->poolName)) {
            $data .= '&poolName=' . urlencode($this->poolName);
        }

        if (!empty($object->ReplyTo)) {
            $replyToTmp = reset($object->ReplyTo);
            $data .= '&replyTo=' . urlencode($replyToTmp[0]);
            if (!empty($replyToTmp[1])) {
                $data .= '&replyToName=' . urlencode($replyToTmp[1]);
            }
        }

        if (!empty($object->Sender)) {
            $data .= '&sender=' . urlencode($object->Sender);
        }

        if (!empty($object->Subject)) {
            $data .= '&subject=' . urlencode($object->Subject);
        }

        $to = array_merge($object->to, $object->cc, $object->bcc);
        $data .= '&to=';
        foreach ($to as $oneRecipient) {
            $data .= urlencode($object->addrFormat($oneRecipient) . ';');
        }
        $data = trim($data, ';');

        $header = "POST /v2/email/send HTTP/1.0\r\n";
        $header .= "Host: api.elasticemail.com\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Connection: Keep-Alive\r\n";
        $header .= 'Content-Length: ' . strlen($data) . "\r\n\r\n";
        $info = $header . $data;
        $result = $this->sendinfo($info);

        if (empty($result['success'])) {
            $this->error = $result;

            return false;
        } else {
            return true;
        }
    }

    function getCredits($object)
    {
        $header = 'GET /v2/account/load?apikey=' . urlencode($this->Password) . " HTTP/1.0\r\n";
        $header .= "Host: api.elasticemail.com\r\n";
        $header .= "Connection: Close\r\n\r\n";
        $result = $this->sendinfo($header);

        return isset($result['Credit']) ? $result['Credit'] : false;
    }

    private function connect()
    {
        if (is_resource($this->conn)) {
            return true;
        }

        $this->conn = fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 20);
        if (!$this->conn) {
            $this->error = 'Could not open connection ' . $errstr;

            return false;
        }

        return true;
    }

    private function sendinfo(&$info)
    {
        //Check if the connection is Ok... and if not we return false.
        if (!$this->connect()) {
            return false;
        }

        $res = '';
        $length = 0;
        ob_start();
        $result = fwrite($this->conn, $info);
        $errorContent = ob_get_clean();
        if ($result === false) {
            return $errorContent;
        }

        while (!feof($this->conn)) {
            $res .= fread($this->conn, 1024);
            if (substr($res, 0, 4) == 'HTTP') {
                $length = 0;
            }
            if ($length == 0) {
                $pos = strpos(strtolower($res), 'content-length:');
                if ($pos !== false) {
                    $lng = substr($res, $pos + 16, 6);
                    if (strpos($lng, "\r") !== false) {
                        $length = (int) $lng;
                        $length += $pos;
                    }
                }
            }
            if ($length > 0 && strlen($res) >= $length) {
                break;
            }
        }

        $answer = explode("\r\n\r\n", $res);
        if (empty($answer[1])) {
            return acymailing_translation('ACY_ERROR');
        }
        $decodedAnswer = json_decode($answer[1], true);
        if (empty($decodedAnswer)) {
            return acymailing_translation('ACY_ERROR');
        }

        if (empty($decodedAnswer['success'])) {
            return acymailing_translation_sprintf('ACY_ERROR_OCCURRED_WHILE_CALLING_API', $decodedAnswer['error']);
        }

        return $decodedAnswer;
    }

    function __destruct()
    {
        if (is_resource($this->conn)) {
            fclose($this->conn);
        }
    }
}

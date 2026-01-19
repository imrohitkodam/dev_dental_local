<?php

require_once("../dompdf_config.inc.php");

// We check wether the user is accessing the demo locally
$local = array("::1", "127.0.0.1");
$is_local = in_array($_SERVER['REMOTE_ADDR'], $local);
$_POST["html"] = '<html>
<body>
<h1 style="font-family:verdana">A heading</h1>
<p style="font-family:arial;color:red;font-size:20px;">A paragraph.</p>
<h2 style="background-color:red">This is a heading</h2>
<p style="background-color:green">This is a paragraph.</p>
<h1>My First Heading</h1>

<p>My first paragraph.</p>
<h1>This is heading 1</h1>
<h2>This is heading 2</h2>
<h3>This is heading 3</h3>
<h4>This is heading 4</h4>
<h5>This is heading 5</h5>
<h6>This is heading 6</h6>
<a href="http://www.w3schools.com">
This is a link</a>
<img src="test.jpg" width="104" height="142" />
<p>This is<br />a para<br />graph with line breaks</p>
<p><b>This text is bold</b></p>
<p><strong>This text is strong</strong></p>
<p><big>This text is big</big></p>
<p><em>This text is emphasized</em></p>
<p><i>This text is italic</i></p>
<p><small>This text is small</small></p>
<p>This is<sub> subscript</sub> and <sup>superscript</sup></p>
<pre>
This is
preformatted text.
It preserves      both spaces
and line breaks.
</pre>

<p>The pre tag is good for displaying computer code:</p>

<pre>
for i = 1 to 10
     print i
next i
</pre>

<p>
<h1>Look! Styles and colors</h1>

<p style="font-family:verdana;text-color:red">
This text is in Verdana and red</p>

<p style="font-family:times;text-color:green">
This text is in Times and green</p>

<p style="font-size:30px">This text is 30 pixels high</p>
</p>
<p></p>

<p><strong>Got a question? </strong>With more than 210,000 members, the Joomla! Discussion Forums at <a href="http://forum.joomla.org/" target="_blank" title="Forums">forum.joomla.org</a> are a great resource for both new and experienced users. Ask your toughest questions the community is waiting to see what you will do with your Joomla! site.</p>
<p><strong>Do you want to show off your new Joomla! Web site?</strong> Visit the <a href="http://forum.joomla.org/viewforum.php?f=514" target="_blank" title="Site Showcase">Site Showcase</a> section of our forum.</p>
<p><strong>Do you want to contribute?</strong></p>
<p>If you think working with Joomla is fun, wait until you start working on it. We are passionate about helping Joomla users become contributors. There are many ways you can help Joomlas development:</p>
<ul>
<li>Submit news about Joomla. We syndicate Joomla-related news on <a href="http://news.joomla.org" target="_blank" title="JoomlaConnect">JoomlaConnect<sup>TM</sup></a>. If you have Joomla news that you would like to share with the community, find out how to get connected <a href="http://community.joomla.org/connect.html" target="_blank" title="JoomlaConnect">here</a>.</li>
<li>Report bugs and request features in our <a href="http://joomlacode.org/gf/project/joomla/tracker/" target="_blank" title="Joomla! developement trackers">trackers</a>. Please read <a href="http://docs.joomla.org/Filing_bugs_and_issues" target="_blank" title="Reporting Bugs">Reporting Bugs</a>, for details on how we like our bug reports served up</li>
<li>Submit patches for new and/or fixed behaviour. Please read <a href="http://docs.joomla.org/Patch_submission_guidelines" target="_blank" title="Submitting Patches">Submitting Patches</a>, for details on how to submit a patch.</li>
<li>Join the <a href="http://forum.joomla.org/viewforum.php?f=509" target="_blank" title="Joomla! development forums">developer forums</a> and share your ideas for how to improve Joomla. We are always open to suggestions, although We are likely to be sceptical of large-scale suggestions without some code to back it up.</li>
<li>Join any of the <a href="http://www.joomla.org/about-joomla/the-project/working-groups.html" target="_blank" title="Joomla! working groups">Joomla Working Groups</a> and bring your personal expertise to the Joomla community. </li>
</ul>
<p>These are just a few ways you can contribute. See <a href="http://www.joomla.org/about-joomla/contribute-to-joomla.html" target="_blank" title="Contribute">Contribute to Joomla</a> for many more ways.</p>
</body>
</html>';
if ( isset( $_POST["html"] )  ) {

  if ( get_magic_quotes_gpc() )
    $_POST["html"] = stripslashes($_POST["html"]);
  
  $dompdf = new DOMPDF();
  $dompdf->load_html($_POST["html"]);
  $dompdf->set_paper($_POST["paper"], $_POST["orientation"]);
  $dompdf->render();

  $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));

  exit(0);
}

?>
<?php include("head.inc"); ?>

<a name="demo"> </a>
<h2>Demo</h2>

<?php if ($is_local) { ?>

<p>Enter your html snippet in the text box below to see it rendered as a
PDF: (Note by default, remote stylesheets, images &amp; inline PHP are disabled.)</p>

<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
<p>Paper size and orientation:
<select name="paper">
<?php
foreach ( array_keys(CPDF_Adapter::$PAPER_SIZES) as $size )
  echo "<option ". ($size == "letter" ? "selected " : "" ) . "value=\"$size\">$size</option>\n";
?>
</select>
<select name="orientation">
  <option value="portrait">portrait</option>
  <option value="landscape">landscape</option>
</select>
</p>

<textarea name="html" cols="60" rows="20">
&lt;html&gt;
&lt;head&gt;
&lt;style&gt;

/* Type some style rules here */

&lt;/style&gt;
&lt;/head&gt;

&lt;body&gt;

&lt;!-- Type some HTML here --&gt;

&lt;/body&gt;
&lt;/html&gt;
</textarea>

<div style="text-align: center; margin-top: 1em;">
  <button type="submit">Download</button>
</div>

</form>
<p style="font-size: 0.65em; text-align: center;">(Note: if you use a KHTML
based browser and are having difficulties loading the sample output, try
saving it to a file first.)</p>

<?php } else { ?>

  <p style="color: red;">
    User input has been disabled for remote connections.
  </p>
  
<?php } ?>

<?php include("foot.inc"); ?>

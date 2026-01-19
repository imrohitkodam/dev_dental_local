<?php
class Doc2Text {
	function getText($file) {
		require_once dirname(__FILE__).'/class.doc2txt.php' ;
		return PHPWordLib::GetPlainText(PHPWordLib::LoadFile($file));
	}
}
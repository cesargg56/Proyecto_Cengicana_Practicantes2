<?php

	function esCsv($filetype)
	{
		$mime_types[0]="text/comma-separated-values";
		$mime_types[1]="text/csv";
		$mime_types[2]="application/csv";
		$mime_types[3]="application/excel";
		$mime_types[4]="application/vnd.ms-excel";
		$mime_types[5]="application/vnd.msexcel";
		$mime_types[6]="text/anytext";
		$esCsv=FALSE;
		for($i=0; $i<7; $i++){
			if(strcmp($mime_types[$i],$filetype)===0){
				$esCsv=TRUE;
			}
		}
		return $esCsv;
	}
?>
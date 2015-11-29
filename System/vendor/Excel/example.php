<?php

        

        //导入execel
        $this->vendor('Excel.PHPExcel');
    	$objReader = \PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
    	$objPHPExcel = $objReader->load('../test.xls');
    	$sheet = $objPHPExcel->getSheet(0);
    	$highestRow = $sheet->getHighestRow(); // 取得总行数
    	$highestColumn = $sheet->getHighestColumn(); // 取得总列数
    	$arr_result=array();
    	$strs=array();
    	 
    	for($j=1;$j<=$highestRow;$j++){
    		for($k='A';$k<= $highestColumn;$k++){
    			//读取单元格
    			$arr_result[$j][]= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
    		}
    	}
    	
    	dump($arr_result);
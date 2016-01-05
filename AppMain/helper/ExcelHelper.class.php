<?php

namespace AppMain\helper;

use System\BaseHelper;

class ExcelHelper extends BaseHelper {
    /**
     * 用户信息
     * @param array $list  列表数据
     * @param array $data  标题数据
     * @return unknown
     */
	function PHPExcel($list,$data){
		$this->vendor('Excel.PHPExcel');
		//创建PHPExcel对象
		$objPHPExcel = new \PHPExcel();
		//设置属性
		$objPHPExcel->getProperties()->setCreator('ctos')->setLastModifiedBy('ctos')->setTitle('Office 2007 XLSX Test Document')
		->setSubject('Office 2007 XLSX Test Document')->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
		->setKeywords('office 2007 openxml php')->setCategory('Test result file');
		//设置宽度
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		//设置高度
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
		$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
		// 字体和样式
		$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('A2:I2')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A2:I2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A2:I2')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
		// 设置水平居中
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//  合并
		$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
		// 表头
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', $data['title'])
		->setCellValue('A2', $data['thematic_name'])
		->setCellValue('B2', $data['bill_sn'])
		->setCellValue('C2', $data['goods_name'])
		->setCellValue('D2', $data['goods_sn'])
		->setCellValue('E2', $data['code'])
		->setCellValue('F2', $data['price'])
		->setCellValue('G2', $data['nickname'])
		->setCellValue('H2', $data['phone'])
		->setCellValue('I2', $data['add_time']);
		// 内容
		for ($i = 0, $len = count($list); $i < $len; $i++) {
			//转换成字符串
			$objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $list[$i]['thematic_name'],\PHPExcel_Cell_DataType::TYPE_STRING);
			
			$objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $list[$i]['bill_sn']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $list[$i]['goods_name']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('D' . ($i + 3), $list[$i]['goods_sn']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $list[$i]['code']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $list[$i]['price']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), $list[$i]['nickname']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), $list[$i]['phone']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), $list[$i]['add_time']);
			$objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':I' . ($i + 3))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':I' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
		}
		 
		// Rename sheet
		// 重命名工作表
		$objPHPExcel->getActiveSheet()->setTitle($data['title']);
		 
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		//设置活动工作表索引到第一个工作表，那么 Excel 就会打开这作为第一个工作表
		$objPHPExcel->setActiveSheetIndex(0);
		 
		// 输出
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $data['title'] . '.xls"');
		header('Cache-Control: max-age=0');
		 
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		$name=time();
		$url=getHost();
		$url=$url.'/excel/'.$name.'.xls';
		$objWriter->save('./excel/'.$name.'.xls');
		return $url;
	}
}

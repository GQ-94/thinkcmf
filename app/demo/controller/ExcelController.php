<?php

namespace app\demo\controller;

use cmf\controller\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use think\Exception;

class ExcelController extends BaseController
{
    /**
     * 导出Excel文件
     */
    public function export()
    {
        try {
            $spread_sheet = new Spreadsheet();
            $sheet        = $spread_sheet->getActiveSheet();

            # 设置文档属性
            $spread_sheet->getDefaultStyle()->getFont()->setName('Consolas')->setSize(12);
            $spread_sheet->getProperties()->setCreator('author')->setLastModifiedBy('author')
                ->setTitle('Title')->setSubject("Subject title")->setDescription('Description')
                ->setKeywords('Keywords')->setCategory('Category');

            # 设置样式
            $sheet->getDefaultColumnDimension()->setWidth(20);
            $sheet->getDefaultRowDimension()->setRowHeight(20);
            $sheet->setTitle('Sheet Name');

            # 设置行标题
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', '用户ID');
            $sheet->setCellValue('C1', '登陆设备');
            $sheet->setCellValue('D1', '登陆时间');
            $sheet->setCellValue('E1', '登陆ip');
            $sheet->getStyle('A1:E1')->getFont()->setBold(true)->setSize(14);

            // 设置内容
            $sheet->fromArray(
                [
                    ['id' => '1', 'user_id' => '100', 'device' => '苹果手机', 'add_time' => date('Y-m-d H:i:s'), 'ip' => '192.168.0.1',],
                    ['id' => '2', 'user_id' => '101', 'device' => '安卓手机', 'add_time' => '', 'ip' => '192.168.0.2',]
                ],
                null,
                'A2'
            );

            // 下载文件
            /*header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition:attachment;filename=download.xlsx');
            header('Cache-Control:max-age=0');
            $writer = IOFactory::createWriter($spread_sheet, 'Xlsx');
            $writer->save('php://output');*/

            // 保存文件
            $writer = IOFactory::createWriter($spread_sheet, "Xlsx");
            $writer->save("upload/download.xlsx");
        } catch (\PhpOffice\PhpSpreadsheet\Exception $sheet_exception) {
        } catch (Exception $exception) {
        }
        exit();
    }


    /**
     * 读取Excel文件
     */
    public function read()
    {
        try {
            $file_path    = WEB_ROOT . 'upload/download.xlsx';
            $spread_sheet = IOFactory::load($file_path);
            for ($i = 0; $i < $spread_sheet->getSheetCount(); $i++) {
                $spread_sheet->setActiveSheetIndex($i);
                $sheet_data = $spread_sheet->getActiveSheet()->toArray(null, true, true, true);
                // print_r($sheet_data);
            }
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        }
    }
}
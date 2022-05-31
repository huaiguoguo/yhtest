<?php
/**
 * @Author: masoner
 * @Date 2022/5/30 11:53
 * @CourseTitle xxxx
 */

namespace frontend\controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use common\models\Supplier;
use yii\helpers\Json;

class GridController extends \yii\web\Controller
{

    public function actionIndex(): string
    {
        $data = [];

        $supplier = new Supplier();
        $provider = $supplier->search(Yii::$app->request->getQueryParams());

        $data['provider'] = $provider;
        $data['supplierModel'] = $supplier;

        return $this->render('index', $data);
    }

    public function actionExport(): string
    {
        $postData = Yii::$app->request->post();

        $selectedIds = $postData['selectedIds'];
        $columns = $postData['columns'];


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($columns as $key => $value) {
            $index = $key + 1;
            if ($index == 1) {
                $title = 'A1';
            } else if ($index == 2) {
                $title = 'B1';
                $value = '名称';
            } elseif ($index == 3) {
                $title = 'C1';
            } elseif ($index == 4) {
                $title = 'D1';
                $value = '状态';
            }
            $sheet->setCellValue($title, $value);
        }

        $selectedIdsArr = explode(',', $selectedIds);

        $dataList = Supplier::find()->select($columns)->andFilterWhere(['in', 'id', $selectedIdsArr])->asArray()->all();

        $columnsCount = count($selectedIdsArr);

        foreach ($dataList as $key => $value) {
            // var_dump($value);
            $index = $key + 2;

            $sheet->setCellValue("A${index}", $value['id']);

            $x = 2;
            while ($x<$columnsCount){
                $cell = $sheet->getCell([$x, 1]);
                $column = $cell->getColumn();
                if ($cell->getValue() == '名称'){
                    $sheet->setCellValue("$column${index}", $value['name']);
                }
                if ($cell->getValue() == 'code'){
                    $sheet->setCellValue("$column${index}", $value['code']);
                }
                if ($cell->getValue() == 't_status'){
                    $sheet->setCellValue("$column${index}", $value['t_status']);
                }
                $x++;
            }
        }

        $sheet->getStyle('A1:N300')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $write = new Xlsx($spreadsheet);
        try {

            $filename = '供应商列表' . date('Ymdhis', time()) . '.xlsx';
            // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
            // header('Content-Disposition: attachment;filename="' . $file . '"');//告诉浏览器输出浏览器名称
            // header('Cache-Control: max-age=0');//禁止缓存
            // ob_start();
            // $write->save('php://output');
            // $content = ob_get_contents();
            // ob_end_clean();

            $path = 'export/';
            $write->save($path . $filename);
        } catch (Exception $e) {
        }

        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $path . $filename;
        return Json::encode(['filename' => $filename, 'fileurl' => $url]);
    }

}
<?php

namespace App\Http\Controllers;

use App\Models\JsonFile;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Storage;

class ExcelController extends Controller
{
    public function select_file()
    {
        return view('select_file');
    }

    public function upload_file(Request $request)
    {
        $file = $request->uploadFile;
        if($file->isValid())
        {
            $file->getRealPath();
            $excel = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $file->getSize();
            $file->getMimeType();
            if(in_array($extension, ['csv', 'xls', 'xlsx']))
            {
                $json = uniqid();
                // $dd = file_get_contents($file->getRealPath());
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
                Storage::disk('public')->put($json.'.json', json_encode($sheetData));
                $json_file = new JsonFile;
                $json_file->excel_file_name = $excel;
                $json_file->json_file_name = $json;
                if($json_file->save())
                {
                    return response()->json(['success'=>true, 'data'=>$json_file]);
                }
                else
                {
                    return response()->json(['success'=>false, 'msg'=>'Some problem in insertion!']);
                }
            }
            else
            {
                return response()->json(['success'=>false, 'msg'=>'Only excel files are allowed!']);
            }
        }
        else
        {
            return response()->json(['success'=>false, 'msg'=>'Only excel files are allowed!']);
        }
    }

    public function sheet(Request $request)
    {
        $inputFileType = 'Xlsx';
        $inputFileName = 'sampleexcel.xlsx';
        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getSheet(0);//
        // Get the highest row and column numbers referenced in the worksheet
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $data = array();

        for ($row = 1; $row <= $highestRow; $row++) {
            $riga = array();
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $riga[] = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
            // if (1 === $row) {
            //     // Header row. Save it in "$keys".
            //     $keys = $riga;
            //     continue;
            // }
            // This is not the first row; so it is a data row.
            // Transform $riga into a dictionary and add it to $data.
            $data[] = $riga;//array_combine($keys, $riga);
        }
        // print_r(json_encode($data));
        // exit;
        $page_data['data'] = $data;
        $page_data['f'] = $request->f;
        return view('sheet', $page_data);
        # code...
    }

    public function xlsx(Request $request)
    {
        // $inputFileType = 'Xlsx';
        // $inputFileName = 'sampleexcel.xlsx';
        // $filename = 'excel';

        // $spreadsheet = IOFactory::load($inputFileName);
        // $spreadsheet = $objReader->load($inputFileName);

        // $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        // Storage::disk('public')->put('excel.json', json_encode($sheetData));
        $s = Storage::disk('public')->get($request->f.'.json');//file_get_contents('excel.json');
        $ss = json_decode($s);
        return response()->json($ss);

        // $writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        // $writer->save('php://output');
        // die();
    }

    public function xlsxupd(Request $request)
    {
        $ri = $request->ri;
        $ci = $request->ci;
        $v = $request->v;
        $f = $request->f;
        $s = Storage::disk('public')->get($f.'.json');//file_get_contents('excel.json');
        $ss = json_decode($s);
        $ss[$ri][$ci] = $v;
        Storage::disk('public')->put($f.'.json', json_encode($ss));
        return response()->json(['success'=>true]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;

class GeneralController extends Controller
{
    public function pdfDataExtractor1(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:2048', // Assuming maximum file size is 2MB
        ]);
        $pdfFile = $request->file('pdf_file');
        $tempPath = $pdfFile->store('temp'); // Move the uploaded file to a temporary location
        $pdfPath = storage_path('app/' . $tempPath);
        $parser = new Parser(); // Initialize the PDF parser
        try {
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText(); // Extract text from the PDF
            $rows = explode("\n", $text);
            dump('explode by string', $rows);

            // Initialize an array to hold the extracted data
            $data = [];
            $headerFound = false;

            // Iterate through each row
            foreach ($rows as $row) {
                $columns = explode("\t", $row);
                $columns = array_map('trim', $columns);
                $data[] = $columns;
            }
            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to extract data from PDF.'], 500);
        } finally {
            if (file_exists($pdfPath)) { // Delete the temporary PDF file
                unlink($pdfPath);
            }
        }
    }

    public function pdfDataExtractor(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:2048', // Assuming maximum file size is 2MB
        ]);
        $pdfFile = $request->file('pdf_file');
        $tempPath = $pdfFile->store('temp'); // Move the uploaded file to a temporary location
        $pdfPath = storage_path('app/' . $tempPath);
        $parser = new Parser(); // Initialize the PDF parser
        try {
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText(); // Extract text from the PDF
            // return $text;
            dump('$text', $text);
            $rows = explode("\n", $text);
            // dd('rows', $rows);

            // Initialize an array to hold the extracted data
            $data = [];
            $headerFound = false;
            $columnNameIndices = []; // Add an array to store column name indices
            $arr = [];
            // Iterate through each row
            foreach ($rows as $rowIndex => $row) {
                // dd($row);
                // if( $row == "Your Current Account Transactions  معاملات حسابكم الجاري"){
                if( $row == "The National Bank of Ras Al Khaimah (P.S.C) (the ªBankº or
                ªRAKBANKº), is a commercial bank regulated and licensed by the
                Central Bank of the UAE."){
                    dump('h1');
                    $arr[] = $row; 
                    dump('$arr',$arr);
                    $columns = explode("\t", $row);
                    $columns = array_map('trim', $columns);
                    $data[] = $columns;
                }
            }
            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to extract data from PDF.'], 500);
        } finally {
            if (file_exists($pdfPath)) { // Delete the temporary PDF file
                unlink($pdfPath);
            }
        }
    }
}

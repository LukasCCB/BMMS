<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Number;
use Illuminate\Support\Facades\Validator;

class RegisterNumberController extends Controller
{
    public function addBulkNumber(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'file' => 'required|mimes:txt,csv,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $file = $request->file('file');
        $numbersArray = [];

        // Identifica o tipo de arquivo e processa
        if ($file->getClientOriginalExtension() == 'txt' || $file->getClientOriginalExtension() == 'csv') {
            $content = file($file->getRealPath());

            foreach ($content as $line) {
                $numbers = explode(',', trim($line));
                foreach ($numbers as $number) {
                    $numbersArray[] = trim($number);
                }
            }
        } elseif ($file->getClientOriginalExtension() == 'docx') {

            // Process docx file (requires an external library to be installed)
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getRealPath());
            $text = '';
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . ' ';
                    }
                }
            }

            $numbersArray = explode(',', trim($text));
        }

        // Remove duplicates and invalid numbers
        $numbersArray = array_unique(array_filter($numbersArray, function ($value) {
            return preg_match('/^[0-9]+$/', $value);
        }));

        foreach ($numbersArray as $number) {
            // Check if number already exists
            if (!Number::where('number', $number)->exists()) {
                Number::create([
                    'name' => $request->name,
                    'number' => $number,
                    'is_active' => true,
                    'is_whatsapp' => true, // default value
                ]);
            }
        }

        return response()->json(['message' => 'Successfully registered numbers.'], 200);
    }
}

<?php

/**
 * Barcode Helper

 * Helper untuk generate barcode menggunakan JsBarcode library
 */

if (!function_exists('generate_barcode_script')) {
    /**
     * Generate JavaScript code untuk membuat barcode dari data array
     * 
     * @param array $data Array data obat atau item lainnya
     * @param array $options Konfigurasi barcode dan field mapping
     * @return string JavaScript code untuk generate barcode
     */
    function generate_barcode_script($data = [], $options = [])
    {
        // Default options
        $default_options = [
            // Field mapping
            'id_field' => 'id_obat',
            'name_field' => 'nama_obat',
            
            // Barcode options
            'format' => 'CODE128',
            'height' => 40,
            'width' => 1.5,
            'lineColor' => '#000000',
            'displayValue' => false,
            'margin' => 0,
            
            // Error handling
            'error_message' => 'Error: Barcode tidak dapat dibuat'
        ];
        
        $options = array_merge($default_options, $options);
        
        // Mulai generate script
        $script = "document.addEventListener('DOMContentLoaded', function() {\n";
        
        if (!empty($data) && is_array($data)) {
            foreach ($data as $item) {
                $id = $item[$options['id_field']] ?? '';
                $name = $item[$options['name_field']] ?? '';
                
                // Skip jika ID kosong
                if (empty($id)) continue;
                
                $barcode_value = $id . '-' . $name;
                
                $script .= "    try {\n";
                $script .= "        new JsBarcode(\"#barcode-{$id}\", \"{$barcode_value}\", {\n";
                $script .= "            format: '{$options['format']}',\n";
                $script .= "            height: {$options['height']},\n";
                $script .= "            width: {$options['width']},\n";
                $script .= "            lineColor: '{$options['lineColor']}',\n";
                $script .= "            displayValue: " . ($options['displayValue'] ? 'true' : 'false') . ",\n";
                $script .= "            margin: {$options['margin']}\n";
                $script .= "        });\n";
                $script .= "    } catch(e) {\n";
                $script .= "        console.error(\"Exception with Barcode generation for ID: {$id}\", e);\n";
                $script .= "        var element = document.getElementById(\"barcode-{$id}\");\n";
                $script .= "        if(element) {\n";
                $script .= "            element.innerHTML = '<span class=\"text-danger\">{$options['error_message']}</span>';\n";
                $script .= "        }\n";
                $script .= "    }\n\n";
            }
        }
        
        $script .= "});";
        
        return $script;
    }
}

if (!function_exists('barcode_svg_element')) {
    /**
     * Generate SVG element untuk menampung barcode
     * 
     * @param string|int $id ID untuk element barcode
     * @param string $class CSS class untuk SVG element
     * @return string HTML SVG element
     */
    function barcode_svg_element($id, $class = 'img-fluid')
    {
        return "<svg id=\"barcode-{$id}\" class=\"{$class}\"></svg>";
    }
}

if (!function_exists('include_jsbarcode_cdn')) {
    /**
     * Generate script tag untuk include JsBarcode dari CDN
     * 
     * @param string $version Versi JsBarcode (default: 3.11.5)
     * @return string HTML script tag
     */
    function include_jsbarcode_cdn($version = '3.11.5')
    {
        return "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/{$version}/JsBarcode.all.min.js\"></script>";
    }
}

if (!function_exists('barcode_table_cell')) {
    /**
     * Generate table cell dengan SVG barcode element
     * Khusus untuk penggunaan dalam tabel
     * 
     * @param string|int $id ID untuk barcode
     * @param string $class CSS class tambahan untuk cell
     * @return string HTML table cell dengan SVG
     */
    function barcode_table_cell($id, $class = '')
    {
        $cellClass = !empty($class) ? " class=\"{$class}\"" : "";
        return "<td{$cellClass}>" . barcode_svg_element($id) . "</td>";
    }
}
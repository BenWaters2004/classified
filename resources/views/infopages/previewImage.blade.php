
            @php
            $extension = substr($documentPath, strrpos($documentPath, '.') + 1);
            $fileName = $documentName.'.'.$extension;
            $maxRead = 100 * 1024 * 1024; // 100MB
            $fh = fopen($documentPath, 'r');

            if(in_array(strtolower($extension),['jpg','jpeg'])){
              header("Content-type: image/jpeg");
              header('Content-Disposition: inline; filename="' . $fileName . '"');
            }
            else if(in_array(strtolower($extension),['png'])){
              header("Content-type: image/png");
              header('Content-Disposition: inline; filename="' . $fileName . '"');
            }
            else if(in_array(strtolower($extension),['gif'])){
              header("Content-type: image/gif");
              header('Content-Disposition: inline; filename="' . $fileName . '"');
            }


            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($documentPath));
            ob_clean();
            flush(); // Flush system output buffer
            readfile($documentPath);
            exit;
            @endphp

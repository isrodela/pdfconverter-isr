<?php
namespace Sample;

require_once 'EasyPdfCloud/autoload.php';
require 'vendor/autoload.php';


class Program
{
    //private static $SCMClientID = 'c82b6582-ef2c-45f5-beda-d76804750fa7';
    //private static $SCMKey = '5c833d4bffd74e8eabe38ec54f37f18ayNrsfzL6dWamdFmEsgtDnnje7W4Ht2xvMgBZNp0vtnOUnqhm47BWntpKnMvJ4ENlipZ7EN1Cq7UAZP0QzRNw0bHd00vA4r0p';

    //////////////////////////////////////////////////////////////////////
    //
    // !!! VERY IMPORTANT !!!
    //
    // You must configure these variables before running this sample code.
    //
    //////////////////////////////////////////////////////////////////////

    // Client ID. You can get your client ID from the developer page
    // (for registered user, you must sign in before visiting this page)
    // https://www.easypdfcloud.com/developer
    //
    // To test the sample code quickly for demo purpose, you can use
    // the following demo client ID:
    //
    //   05ee808265f24c66b2b8e31d90c31ab1
    //
    private static $clientId = '1ae9c821a6194266a30fee756c14c446';

    // Client secret. You can get your client secret from the developer page
    // (for registered user, you must sign in before visiting this page)
    // https://www.easypdfcloud.com/developer
    //
    // To test the sample code quickly for demo purpose, you can use
    // the following demo client secret:
    //
    //   16ABE87E052059F147BB2A491944BF7EA7876D0F843DED105CBA094C887CBC99
    //
    private static $clientSecret = '45E0EA759061041888E65CDFBFC49AC6F6D669B2CE34DFA0F6A6CD10F4819F5F';

    // Workflow ID. You can get your workflow ID from the developer page
    // (for registered user, you must sign in before visiting this page)
    // https://www.easypdfcloud.com/developer
    //
    // To test the sample code quickly for demo purpose, you can use
    // one of the following demo workflow IDs:
    //
    //  00000000048585C1 : "Convert Files to PDF" workflow
    //  00000000048585C2 : "Convert PDF to Word" workflow
    //  00000000048585C3 : "Combine Files to PDF" workflow
    //
    private static $workflowId = '000000000697468C';

    // A flag indicating if the specified workflow contains the "Combine PDFs" task
    //
    // true:  The specified workflow contains the "Combine PDFs" task
    // false: The specified workflow does not contain the "Combine PDFs" task
    //
    private static $workflowIncludesCombinePdfsTask = false;
    
    // A flag to specify whether to enable test mode for job execution.
    //
    // true:  Enable test mode (do not use API credits)
    // false: Do not enable test mode (use API credits)
    //
    // If true is specified, then the job is executed as a test mode and
    // your API credit will not be used.
    //
    private static $enableTestMode = true;

    // Path to the input file which you want to upload and process
    // This value is used only if the $workflowIncludesCombinePdfsTask == false
    //
    // To test the sample code quickly for demo purpose, you can use
    // one of the following files bundled with this sample:
    //
    // samples/input/sample.docx : For converting Word to PDF
    // samples/input/sample.pdf  : For converting PDF to Word
    //
    private static $inputFilePath = 'samples/input/sample.pdf';

    // List of path to the input file which you want to upload and process
    // This value is used only if the $workflowIncludesCombinePdfsTask == true
    //
    // To test the sample code quickly for demo purpose, you can use
    // the following files bundled with this sample:
    //
    // samples/input/sample.docx : A sample Word file
    // samples/input/sample.pdf  : A sample PDF file
    //
    private static $inputFilePaths = array(
        'samples/input/sample.docx',
        'samples/input/sample.pdf',
    );

    // Output directory used for saving output file. Make sure that you
    // have sufficient permission to create and write to this directory
    private static $outputDir = 'samples/output';

    // A flag indicating whether to push output file to the client when
    // this sample code is running from a web server
    //
    // true:  Push output to the client when running from a web server
    // false: Always save output to the local location specified by $outputDir
    //
    // For debugging purpose, setting this value to false is recommended
    // when running this sample code for the first time
    private static $pushOutputToClientIfRunningFromWebServer = false;

    //////////////////////////////////////////////////////////////////////

    private static function isRunningFromConsole()
    {
        return (\php_sapi_name() == 'cli');
    }

    //////////////////////////////////////////////////////////////////////

    private static function eol()
    {
        return (static::isRunningFromConsole() ? PHP_EOL : '<br />');
    }

    //////////////////////////////////////////////////////////////////////

    private static function shouldSaveOutputToLocalStorage()
    {
        if (static::isRunningFromConsole()) {
            return true;
        }

        if (!static::$pushOutputToClientIfRunningFromWebServer) {
            return true;
        }

        return false;
    }

    //////////////////////////////////////////////////////////////////////

    private static function debugPrintLine($string = null)
    {
        if (static::shouldSaveOutputToLocalStorage()) {
            static::printLine($string);
        }
    }

    //////////////////////////////////////////////////////////////////////

    private static function printLine($string = null)
    {
        echo (\mb_strlen($string, 'utf-8') > 0 ? $string : '') . static::eol();
    }

    //////////////////////////////////////////////////////////////////////

    private static function getStackTrace($exception)
    {
        $prefix = '';
        $postfix = '';

        if (!static::isRunningFromConsole()) {
            $prefix = '<pre>';
            $postfix = '</pre>';
        }

        $trace = $prefix . $exception->getTraceAsString() . $postfix;
        return $trace;
    }

    //////////////////////////////////////////////////////////////////////

    private static function printException($message, $exception)
    {
        static::printLine();
        static::printLine($message);
        static::printLine('Message: ' . $exception->getMessage());
        static::printLine();
        static::printLine('Stack trace: ');
        static::printLine(static::getStackTrace($exception));
        static::printLine();
    }

    //////////////////////////////////////////////////////////////////////

    private static function checkParameters()
    {
        static::debugPrintLine('--------------------------------------------------------');
        static::debugPrintLine('App parameters:');
        static::debugPrintLine();
        static::debugPrintLine('clientId: ' . static::$clientId);
        static::debugPrintLine('clientSecret: ' . (\mb_strlen(static::$clientSecret, 'utf-8') == 0 ? '' : '********'));
        static::debugPrintLine('workflowId: ' . static::$workflowId);
        static::debugPrintLine('enableTestMode: ' . (static::$enableTestMode ? 'true' : 'false'));

        if (static::$workflowIncludesCombinePdfsTask == false) {
            static::debugPrintLine('inputFilePath: ' . static::$inputFilePath);
        } else {
            static::debugPrintLine('inputFilePaths:');
            foreach (static::$inputFilePaths as $filePath) {
                static::debugPrintLine('    ' . $filePath);
            }
        }

        static::debugPrintLine('outputDir: ' . static::$outputDir);
        static::debugPrintLine('--------------------------------------------------------');
        static::debugPrintLine();

        if (\mb_strlen(static::$clientId, 'utf-8') == 0) {
            throw new \InvalidArgumentException('clientId is not specified in the code!');
        }

        if (\mb_strlen(static::$clientSecret, 'utf-8') == 0) {
            throw new \InvalidArgumentException('clientSecret is not specified in the code!');
        }

        if (static::$workflowIncludesCombinePdfsTask == false) {
            if (\mb_strlen(static::$inputFilePath, 'utf-8') == 0) {
                throw new \InvalidArgumentException('inputFilePath is not specified in the code!');
            }

            if (\is_file(static::$inputFilePath) == false) {
                throw new \InvalidArgumentException(static::$inputFilePath . ' does not exist');
            }
        } else {
            if (\count(static::$inputFilePaths) == 0) {
                throw new \InvalidArgumentException('inputFilePaths is not specified in the code!');
            }

            foreach (static::$inputFilePaths as $filePath) {
                if (\is_file($filePath) == false) {
                    throw new \InvalidArgumentException($filePath . ' does not exist');
                }
            }
        }

        if (\mb_strlen(static::$outputDir, 'utf-8') == 0) {
            throw new \InvalidArgumentException('outputDir is not specified in the code!');
        }

        if (static::shouldSaveOutputToLocalStorage()) {
            if (\is_dir(static::$outputDir) == false) {
                if (\mkdir(static::$outputDir, 0755, true) == false) {
                    static::debugPrintLine();
                    static::debugPrintLine('Unable to create output directory');
                    static::debugPrintLine('Make sure you have sufficient permission to create the output directory.');
                    static::debugPrintLine();
                }
            }
        }
    }

    //////////////////////////////////////////////////////////////////////

    private static function checkCreditsInfo($jobInfo)
    {
        $jobInfoDetail = $jobInfo->getDetail();
        
        if ($jobInfoDetail != null) {
            $apiCredits = $jobInfoDetail->getApiCredits();
            $ocrCredits = $jobInfoDetail->getOcrCredits();
            
            if (($apiCredits != null) || ($ocrCredits != null))
            {
                static::debugPrintLine();
            
                if ($apiCredits != null) {
                    // Check API credits
                    
                    static::debugPrintLine('API credits remaining: ' . $apiCredits->getCreditsRemaining());

                    if ($apiCredits->getNotEnoughCredits())
                    {
                        static::debugPrintLine("Not enough API credits!");
                    }

                    static::debugPrintLine();
               }
                
                if ($ocrCredits != null) {
                    // Check OCR credits

                    static::debugPrintLine('OCR credits remaining: ' . $ocrCredits->getCreditsRemaining());

                    if ($ocrCredits->getNotEnoughCredits())
                    {
                        static::debugPrintLine("Not enough OCR credits!");
                    }

                    static::debugPrintLine();
               }
            }
        }
    }
    
    //////////////////////////////////////////////////////////////////////

    private static function saveOutput(\Bcl\EasyPdfCloud\FileData $fileData, $outputDir)
    {
        $fileName = $fileData->getName();
        $contents = $fileData->getContents();
        $fileSize = $fileData->getBytes();

        static::debugPrintLine('Job execution completed');
        static::debugPrintLine('Output file name: ' . $fileName);
        static::debugPrintLine('Output file size: ' . \number_format($fileSize) . ' bytes');

        if (static::shouldSaveOutputToLocalStorage()) {
            static::debugPrintLine('Saving to output directory...');

            $outputFilePath = \rtrim($outputDir, '/\\') . DIRECTORY_SEPARATOR . $fileName;

            // Save output to local directory
            if (\file_put_contents($outputFilePath, $contents) == false) {
                static::debugPrintLine();
                static::debugPrintLine('Unable to save output file to output directory');
                static::debugPrintLine('Make sure you have sufficient permission to write to the output directory.');
                static::debugPrintLine();
                return;
            }

            static::debugPrintLine('Output saved to: ' . $outputFilePath);
            return;
        }

        // Push output to the client
        \header('Content-Type: ' . 'application/octet-stream');
        \header('Content-Length: ' . $fileSize);
        \header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        echo $contents;
    }

    //////////////////////////////////////////////////////////////////////

    private static function executeNewJob($clientId, $clientSecret, $workflowId, $enableTestMode, $inputFilePath, $outputDir)
    {
        // Create easyPDF Cloud client object
        $client = new \Bcl\EasyPdfCloud\Client($clientId, $clientSecret);

        // Upload input file and start new job
        $job = $client->startNewJobWithFilePath($workflowId, $inputFilePath, $enableTestMode);

        static::debugPrintLine('New job started (job ID: ' . $job->getJobId(). ')');
        static::debugPrintLine('Waiting for job execution completion...');

        // Wait until job execution is completed
        $jobExecutionResult = $job->waitForJobExecutionCompletion();

        // Check API/OCR credits info
        static::checkCreditsInfo($jobExecutionResult->getJobInfo());
        
        static::saveOutput($jobExecutionResult->getFileData(), $outputDir);
    }

    //////////////////////////////////////////////////////////////////////

    private static function executeNewJobForMergeTask($clientId, $clientSecret, $workflowId, $enableTestMode, array $inputFilePaths, $outputDir)
    {
        // Create easyPDF Cloud client object
        $client = new \Bcl\EasyPdfCloud\Client($clientId, $clientSecret);

        // Upload input file and start new job
        $job = $client->startNewJobForMergeTask($workflowId, $inputFilePaths, $enableTestMode);

        static::debugPrintLine('New job started (job ID: ' . $job->getJobId(). ')');
        static::debugPrintLine('Waiting for job execution completion...');

        // Wait until job execution is completed
        $jobExecutionResult = $job->waitForJobExecutionCompletion();

        // Check API/OCR credits info
        static::checkCreditsInfo($jobExecutionResult->getJobInfo());
        
        static::saveOutput($jobExecutionResult->getFileData(), $outputDir);
    }

    //////////////////////////////////////////////////////////////////////

    public static function main()
    {
        // Set current working directory to the same location as this file
        // so that the default sample input file and output directory
        // (both using relative path) can be found
        \chdir(__DIR__);

        //get GUID
        $docGUID = $_GET["guid"];        
        static::debugPrintLine('$docGUID'.' | '.$docGUID);

        //make call to get document from spring

        $authBody='{"client_id":"c82b6582-ef2c-45f5-beda-d76804750fa7","client_secret":"5c833d4bffd74e8eabe38ec54f37f18ayNrsfzL6dWamdFmEsgtDnnje7W4Ht2xvMgBZNp0vtnOUnqhm47BWntpKnMvJ4ENlipZ7EN1Cq7UAZP0QzRNw0bHd00vA4r0p"}';
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://authuat.springcm.com/api/v201606/',
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'     => 'application/json'
            ],
            ['body' => $authBody]
        ]);

        $response = $client->request('POST', 'apiuser');
        
        static::debugPrintLine("status | ".$response->getStatusCode());

        static::debugPrintLine();
        static::debugPrintLine('Working directory: ' . \getcwd());
        static::debugPrintLine();

        try {
            // Check user parameters
            static::checkParameters();
        } catch (\Exception $e) {
            static::printException('Parameter check failed!', $e);
            return;
        }

        try {
            static::debugPrintLine('Executing new job...');

            if (static::$workflowIncludesCombinePdfsTask == false) {
                // The workflow does not include the "Combine PDFs" task

                // Start job execution
                static::executeNewJob(
                    static::$clientId,
                    static::$clientSecret,
                    static::$workflowId,
                    static::$enableTestMode,
                    static::$inputFilePath,
                    static::$outputDir
                );
            } else {
                // The workflow includes the "Combine PDFs" task

                // Start job execution
                static::executeNewJobForMergeTask(
                    static::$clientId,
                    static::$clientSecret,
                    static::$workflowId,
                    static::$enableTestMode,
                    static::$inputFilePaths,
                    static::$outputDir
                );
            }
        } catch (\Bcl\EasyPdfCloud\EasyPdfCloudApiException $e) {
            static::printException('API execution failed!', $e);
        } catch (\Bcl\EasyPdfCloud\JobExecutionException $e) {
            static::printException('Job execution failed!', $e);
            // Check API/OCR credits info
            static::checkCreditsInfo($e->getJobInfo());
        } catch (\Bcl\EasyPdfCloud\ApiAuthorizationException $e) {
            static::printException('API Authorization failed!', $e);
        } catch (\Exception $e) {
            static::printException('Uncaught exception!', $e);
        }

        static::debugPrintLine('Done');
    }

    //////////////////////////////////////////////////////////////////////
}

// Call the main function to start the sample code execution
Program::main();

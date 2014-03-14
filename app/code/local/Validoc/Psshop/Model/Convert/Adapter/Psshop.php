<?php

class Validoc_Psshop_Model_Convert_Adapter_Psshop extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    const XML_PATH_EXPORT_LOCAL_VALID_PATH = 'general/file/importexport_local_valid_paths';

    /**
     * Te devuelve o te carga un recurso el cual te permite leer o escribir archivos, ademas de cargar el recurso
     * verifica que la direccion de la variable path este disponible.
     * @return Varien_Io_Abstract
     */
    public function getResource($forWrite = false)
    {
        $this->setVar('filename', Mage::getSingleton('admin/session')->getFilenameResultSoap());
        if (!$this->_resource) {
            Mage::log('get file load');
            Mage::log(Mage::getSingleton('admin/session')->getFilenameResultSoap());
            $type = $this->getVar('type', 'file');
            $className = 'Varien_Io_' . ucwords($type);
            $this->_resource = new $className();

            $isError = false;

            $ioConfig = $this->getVars();
            switch ($this->getVar('type', 'file')) {
                case 'file':
                    //validate export/import path

                    if (isset($ioConfig['filename']) && $ioConfig['filename'] != '') {
                        $path = rtrim($ioConfig['path'], '\\/')
                            . DS . $ioConfig['filename'];
                        /** @var $validator Mage_Core_Model_File_Validator_AvailablePath */
                        $validator = Mage::getModel('core/file_validator_availablePath');
                        $validator->setPaths( Mage::getStoreConfig(self::XML_PATH_EXPORT_LOCAL_VALID_PATH) );
                        if (!$validator->isValid($path)) {
                            foreach ($validator->getMessages() as $message) {
                                Mage::throwException($message);
                                return false;
                            }
                        }
                    }


                    if (preg_match('#^' . preg_quote(DS, '#').'#', $this->getVar('path')) ||
                        preg_match('#^[a-z]:' . preg_quote(DS, '#') . '#i', $this->getVar('path'))) {

                        $path = $this->_resource->getCleanPath($this->getVar('path'));
                    } else {
                        $baseDir = Mage::getBaseDir();
                        $path = $this->_resource->getCleanPath($baseDir . DS . trim($this->getVar('path'), DS));
                    }

                    $this->_resource->checkAndCreateFolder($path);

                    $realPath = realpath($path);

                    if (!$isError && $realPath === false) {
                        $message = Mage::helper('dataflow')->__('The destination folder "%s" does not exist or there is no access to create it.', $ioConfig['path']);
                        Mage::throwException($message);
                    } elseif (!$isError && !is_dir($realPath)) {
                        $message = Mage::helper('dataflow')->__('Destination folder "%s" is not a directory.', $realPath);
                        Mage::throwException($message);
                    } elseif (!$isError) {
                        if ($forWrite && !is_writeable($realPath)) {
                            $message = Mage::helper('dataflow')->__('Destination folder "%s" is not writable.', $realPath);
                            Mage::throwException($message);
                        } else {
                            $ioConfig['path'] = rtrim($realPath, DS);
                        }
                    }
                    break;
                default:
                    $ioConfig['path'] = rtrim($this->getVar('path'), '/');
                    break;
            }

            if ($isError) {
                return false;
            }
            try {
                $this->_resource->open($ioConfig);
            } catch (Exception $e) {
                $message = Mage::helper('dataflow')->__('An error occurred while opening file: "%s".', $e->getMessage());
                Mage::throwException($message);
            }
        }
        return $this->_resource;
    }

    public function loadRequest()
    {
        $ioConfig = $this->getVars();
        // obtiene el resultado del web service
        $resultSoap = $this->getResultSoap();
        // armar un nombre para el archivo
        $session_id = Mage::getSingleton('admin/session')->getSessionId();
        $user = Mage::getSingleton('admin/session')->getUser();
        $filename = $session_id . '-' .  $user->getId() . '-result.xml';
        // escribe el resultado del webservice que esta en memoria dentro de un archivo con el nombre que esta definido en el anteior bloque
        $result = $this->getResource()->write($filename, $resultSoap);

        // defino la variable FilenameResultSoap dentro de la session del usuario y guardo el nombre del archivo
        Mage::getSingleton('admin/session')->setFilenameResultSoap($filename);
        // crea la ruta hacia el archivo donde esta el xml del web service
        $filename = rtrim($ioConfig['path'], '\\/') . DS . $filename;

        if (false === $result) {
            $message = Mage::helper('dataflow')->__('An ocurred error to save file: "%s".', $filename);
            Mage::throwException($message);
        } else {
            $message = Mage::helper('dataflow')->__('Results mps saved successfully: "%s".', $filename);
            $this->addException($message);
        }

        $this->setData($result);
        return $this;
    }

    public function convertResultToCsv() {
        if (!$this->getResource()) {
            return $this;
        }
        $filename = $this->getVar('filename');

        $result_soap = $this->getResource()->read($filename);
        $result = $this->createFileCsv($result_soap);

        $filename = $this->getResource()->pwd() . '/' . $this->getVar('filename');
        if (false === $result) {
            $message = Mage::helper('dataflow')->__('An occurred error in the conversion: "%s".', $filename);
            Mage::throwException($message);
        } else {
            $message = Mage::helper('dataflow')->__('The conversion was successfully completed: "%s".', $filename);
            $this->addException($message);
        }

        $this->setData($result);
        return $this;
    }

    /**
     * Load data
     *
     * @return Mage_Dataflow_Model_Convert_Adapter_Io
     */
    public function load()
    {
        if (!$this->getResource()) {
            return $this;
        }

        $batchModel = Mage::getSingleton('dataflow/batch');
        $destFile = $batchModel->getIoAdapter()->getFile(true);
        Mage::log('load');
        Mage::log($this->getVar('filename'));
        Mage::log($destFile);
        $result = $this->getResource()->read($this->getVar('filename'), $destFile);
        $filename = $this->getResource()->pwd() . '/' . $this->getVar('filename');
        if (false === $result) {
            $message = Mage::helper('dataflow')->__('Could not load file: "%s".', $filename);
            Mage::throwException($message);
        } else {
            $message = Mage::helper('dataflow')->__('Loaded successfully: "%s".', $filename);
            $this->addException($message);
        }

        $this->setData($result);
        return $this;
    }

    /**
     * Save result to destination file from temporary
     *
     * @return Mage_Dataflow_Model_Convert_Adapter_Io
     */
    public function save()
    {
        if (!$this->getResource(true)) {
            return $this;
        }

        $batchModel = Mage::getSingleton('dataflow/batch');

        $dataFile = $batchModel->getIoAdapter()->getFile(true);

        $result = false;
        if ( $this->getResource()->read($dataFile, $this->getVar('path')) ) {
            $result = true;
        }

        $filename = $batchModel->getIoAdapter()->getFile();

//        $result   = $this->getResource()->write($filename, $dataFile, 0777);

        if (false === $result) {
            $message = Mage::helper('dataflow')->__('Could not save file: %s.', $filename);
            Mage::throwException($message);
        } else {
            $message = Mage::helper('dataflow')->__('Saved successfully: "%s" [%d byte(s)].', $filename, $batchModel->getIoAdapter()->getFileSize());
            if ($this->getVar('link')) {
                $message .= Mage::helper('dataflow')->__('<a href="%s" target="_blank">Link</a>', $this->getVar('link'));
            }
            $this->addException($message);
        }
        return $this;
    }

    private function getResultSoap()
    {
        $client = new SoapClient('http://www.mps.com.co:91/ArticuloDisponible.asmx?WSDL', array("trace" => 1, "exception" => 1, "connection_timeout" => 0) );
        $resultSoap = $client->Disponible()->DisponibleResult->any;

//        Mage::log( 'solicitud xml para fibos:' );
//        Mage::log( $dto->getRequestXML() );
//        Mage::log( 'respuesta de fibos:' );
//        Mage::log( $resultSoap );

        return $resultSoap;
    }

    private function createFileCsv($resultSoap)
    {
        $ioConfig = $this->getVars();

        // config object csv
        $session_id = Mage::getSingleton('admin/session')->getSessionId();
        $user = Mage::getSingleton('admin/session')->getUser();
        $filename = $session_id . '-' .  $user->getId() . '-result.csv';
        Mage::getSingleton('admin/session')->setFilenameResultSoap($filename);
        $mageCsv = new Varien_File_Csv();
        $path = rtrim($this->getVar('path'), '\\/') . DS . $filename;

        $csv_row = array();

        $data['ItemId'] = 'ItemId';
        $data['ItemName'] = 'ItemName';
        $data['CostUnitAmountMST'] = 'CostUnitAmountMST';
        $csv_row[] = $data;

        $xml = new DomDocument();
        $xml->loadXML($resultSoap);
        $tables = $xml->getElementsByTagName( "Table" );

        $id = null;
        foreach($tables as $node){
            $item = $node->getElementsByTagName( "ItemId" );
            $itemValue = $item->item(0)->nodeValue;
            $data['ItemId'] = $itemValue;

            $item = $node->getElementsByTagName( "ItemName" );
            $itemValue = $item->item(0)->nodeValue;
            $data['ItemName'] = $itemValue;

            $item = $node->getElementsByTagName( "CostUnitAmountMST" );
            $itemValue = $item->item(0)->nodeValue;
            $data['CostUnitAmountMST'] = $itemValue;

            $csv_row[] = $data;
        }
        // write to csv file
        $mageCsv->saveData($path, $csv_row); //note $csv_row will be two dimensional array
        return true;
    }
}
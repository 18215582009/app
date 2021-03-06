<?php
    
    $filename=__FILE__;
    $current_directory=dirname($filename);
    $root_directory=  dirname($current_directory);
    
    require_once '../logSystem.php';
    require_once $root_directory.'/modules/reboot_module.php';
    require_once $root_directory.'/modules/getRPCMethods_module.php';
    require_once $root_directory.'/modules/informResponse_module.php';
    require_once $root_directory.'/modules/setParameterValues_module.php';
    require_once $root_directory.'/modules/emptyResponse_module.php';
    require_once $root_directory.'/modules/sessionClose_module.php';
    
    class server_conf extends logSystem{
        
        # Array with all avaible functions that can be inserted into
        # the server_conf.xml
        var $options=array('GetParameterNames',
                            'GetParameterValues',
                            'GetParameterAttributes',
                            'SetParameterValues',
                            'SetParameterAttributes',
                            'AddObject',
                            'DeleteObject',
                            'Download',
                            'ScheduleInform',
                            'Reboot',
                            'FactoryReset',
                            'GetRPCMethods',
                            'InformResponse',
                            'EmptyResponse',
                            'SessionClose');
        
        # Array which holds the obj from the modules
        var $obj_array=array();
        
        # Array which holds the acs parameters
        var $parameter_array=array();
        
        function __construct() {
            
            $this->server_conf_validate_xml_inputs();
            
        }
        
        # Returns the obj array
        public function server_conf_get_obj()
        {
            return $this->obj_array;
        }
        
        # Returns the parameter array
        public function server_conf_get_parameters()
        {
            return $this->parameter_array;
        }
        
        # Validates the functions inserted into the server_conf.xml
        private  function server_conf_validate_xml_inputs()
        {
            $xml_array=array();
            
            $xml_array=$this->server_conf_parse_xml();
            
            foreach($xml_array['acs_functions'] as $functions)
            {                
                $functions=trim($functions);
                
                if(!in_array($functions, $this->options))
                {
                    $this->mlog('Then server_conf.xml file is not valid');
                    die();
                }                
            }
                        
            $this->server_conf_create_objs();
            
        }
        
        # Creates the obj that are defined in the server_conf.xml 
        # and fills in the parameter and obj array
        private function server_conf_create_objs()
        {
            
            $function_array=$this->server_conf_parse_xml();
            
            $this->parameter_array['acs_ip']=$function_array['acs_ip'];
            $this->parameter_array['acs_port']=$function_array['acs_port'];
            $this->parameter_array['cpe_ip']=$function_array['cpe_ip'];
            $this->parameter_array['cpe_port']=$function_array['cpe_port'];
            $this->parameter_array['cpe_path']=$function_array['cpe_path'];
            $this->parameter_array['cpe_auth']=$function_array['cpe_auth'];
            $this->parameter_array['cpe_user']=$function_array['cpe_user'];
            $this->parameter_array['cpe_pass']=$function_array['cpe_pass'];
            $this->parameter_array['acs_functions']=$function_array['acs_functions'];
            
            foreach($function_array['acs_functions'] as $function)
            {
                switch ($function):
                    case 'GetRPCMethods':
                        $getRPCMethodsObj=new getRPCMethods();
                        $this->obj_array['GetRPCMethods']=$getRPCMethodsObj;
                        break;
                    case 'Reboot':
                        $rebootObj=new reboot();
                        $this->obj_array['Reboot']=$rebootObj;
                        break;
                    case 'InformResponse':
                        $informResponseObj=new informResponse();
                        $this->obj_array['InformResponse']=new $informResponseObj;
                        break;
                    case 'SetParameterValues':
                        $setParameterValuesObj=new setParameterValues();
                        $this->obj_array['SetParameterValues']=new $setParameterValuesObj;
                        break;
                    case 'EmptyResponse':
                        $emptyResponseObj=new emptyResponse();
                        $this->obj_array['EmptyResponse']=new $emptyResponseObj;
                        break;
                    case 'SessionClose':
                        $sessionCloseObj=new sessionClose();
                        $this->obj_array['SessionClose']=new $sessionCloseObj;
                        break;
                endswitch;
            }
                                    
        }
        
        # Prses the server_conf.xml file and return an array with the content
        private function server_conf_parse_xml()
        {
            
            $xml_elements=array();                                        
            
            $server_xml = simplexml_load_file('server_conf.xml');
              
            $xml_elements['acs_ip']=(string)$server_xml->acs_ip;
            $xml_elements['acs_port']=(string)$server_xml->acs_port;
            $xml_elements['cpe_ip']=(string)$server_xml->cpe_ip;
            $xml_elements['cpe_port']=(string)$server_xml->cpe_port;
            $xml_elements['cpe_path']=(string)$server_xml->cpe_path;
            $xml_elements['cpe_auth']=(string)$server_xml->cpe_auth;
            $xml_elements['cpe_user']=(string)$server_xml->cpe_user;
            $xml_elements['cpe_pass']=(string)$server_xml->cpe_pass;
            
            $functions=(Array)$server_xml->acs_functions;
                                    
            foreach($functions as $function)
            {
                $xml_elements['acs_functions']=$function;
            }                        
            
            return $xml_elements;

        }
        
    }

?>

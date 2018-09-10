<?php
namespace modules\translator;                       //Make sure namespace is same structure with parent directory

use \classes\Auth as Auth;                          //For authentication internal user
use \classes\JSON as JSON;                          //For handling JSON in better way
use \classes\CustomHandlers as CustomHandlers;      //To get default response message
use \classes\UniversalCache as UniversalCache;      //To reduce the limit rate from Google translate with cache
use PDO;                                            //To connect with database

	/**
     * Translator
     *
     * @package    modules/translator
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2018 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim-modules-translator/blob/master/LICENSE.md  MIT License
     */
    class Translator {

        // data var
        var $source='',$target,$text,$show='original';

        // database var
        protected $db;

        //base var
        protected $basepath,$baseurl,$basemod;

        //master var
		var $username,$token;
        
        //construct database object
        function __construct($db=null) {
			if (!empty($db)) $this->db = $db;
            $this->baseurl = (($this->isHttps())?'https://':'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
            $this->basepath = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']);
			$this->basemod = dirname(__FILE__);
        }

        //Detect scheme host
        function isHttps() {
            $whitelist = array(
                '127.0.0.1',
                '::1'
            );
            
            if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
                if (!empty($_SERVER['HTTP_CF_VISITOR'])){
                    return isset($_SERVER['HTTPS']) ||
                    ($visitor = json_decode($_SERVER['HTTP_CF_VISITOR'])) &&
                    $visitor->scheme == 'https';
                } else {
                    return isset($_SERVER['HTTPS']);
                }
            } else {
                return 0;
            }
        }

        //Get modules information
        public function viewInfo(){
            return file_get_contents($this->basemod.'/package.json');
        }

        public function translateData(){
            if(strtolower($this->show) != 'original') $this->show = 'lite'; 
            $key = strtolower('google_translate_'.$this->lang.'_'.$this->show.'_'.$this->source.'_'.$this->target).'_'.md5(strtolower($this->text));
            if(UniversalCache::isCached($key,84600)){
                $datajson = JSON::decode(UniversalCache::loadCache($key),true);
                $data = $datajson['value'];
            } else {
                $lang = new GoogleTranslate;
                $lang->source = $this->source;
                $lang->target = $this->target;
                $lang->text = $this->text;
                $translated = $lang->translate();
                if(!empty($translated->getText())){
                    if (strtolower($this->show) == 'original'){
                        $data = [
                            'result' => $translated->makeArray(),
                            'status' => 'success',
                            'code' => 'RS501',
                            'message' => CustomHandlers::getreSlimMessage('RS501',$this->lang)
                        ];
                    } else {
                        $data = [
                            'result' => [
                                'text' => $translated->getText(),
                                'source' => $translated->getSource(),
                                'target' => $this->target,
                                'confidence' => $translated->getConfidence()
                            ],
                            'status' => 'success',
                            'code' => 'RS501',
                            'message' => CustomHandlers::getreSlimMessage('RS501',$this->lang)
                        ];
                    }
                    UniversalCache::writeCache($key,$data,84600);
                } else {
                    $data = [
                        'status' => 'error',
                        'code' => 'RS601',
                        'message' => CustomHandlers::getreSlimMessage('RS601',$this->lang)
                    ];
                }
            }
            return $data;
        }

        public function translate(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                $data = $this->translateData();
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401',$this->lang)
				];
            }
			return JSON::encode($data,true);
			$this->db = null;
        }

        public function translatePublic() {
			return JSON::encode($this->translateData(),true);
        }

    }
<?php


    /**
     * 主题：文件夹合并工具 for Mac， 实现复制文件时"保留两个文件"的效果
     * 
     * 作者：Rudon <285744011@qq.com>
     * 创建：2020-03-09
     * 描述：
     *      在Windows系统下从-文件夹A-复制文件到-文件夹B-时，
     *      如果文件logo.jpg重复了，系统会在文件夹B里创建文件'logo(1).jpg'，
     *      但是，Mac系统不会，只能"跳过"，"停止"，"替换"，这个简单的要求"保留"为什么不做出来？
     *      本工具支持多层文件夹合并。
     *      
     * 
     * 【使用方法】
     * 1. 在桌面上新建两个文件夹，取名"from"和"to"，把你需要合并的文件夹、文件统统复制到文件夹"from"
     * 2. 下载本文件"mergeFolders.php"到Macbook上的桌面
     * 3. 打开命令行工具 （按F4 > 文件夹"其它" > 终端）
     * 4. 在Mac中开启PHP语言支持： https://rudon.blog.csdn.net/article/details/104745975
     * 5. 在命令行中，输入命令：php ~/Desktop/mergeFolders.php
     * 6. OK, 稍等片刻，检查文件夹"to"是否包含了所有的文件
     * 
     */

    /* summary.xls => summary(1).xls */
    define('SYMBOL_LEFT',   '(');
    define('SYMBOL_RIGHT',   ')');
    define('FOLDER_FROM',   'from');
    define('FOLDER_TO',     'to');
    
    
    
    
    function a ($v){
        header('Content-Type: text/css; charset=utf-8');
        print_r($v);
        die();
    }
    
    
    $action = new mergeFolder();
    $action->merge();
    $action->echoResult();
    
    

    class mergeFolder {
        public $working_path;
        public $from;
        public $to;
        public $merged_list;
        public $special_cases;
        public $need_log;


        public function __construct() {
            /* 当前文件夹 */
            $this->working_path = dirname(__FILE__).'/';
            $this->from = $this->working_path . FOLDER_FROM . '/';
            $this->to = $this->working_path . FOLDER_TO . '/';
            $this->merged_list = array();
            $this->special_cases = array();
            $this->need_log = true;
            
            $this->check_folders();
        }
        
        public function check_folders () {
            if (!is_dir($this->from)) {
                $this->echoError('找不到文件夹`'.FOLDER_FROM.'`');
            }
            if (!is_dir($this->to)) {
                $this->echoError('找不到文件夹`'.FOLDER_TO.'`');
            }
        }
        
        public function echoError ($message) {
            die( PHP_EOL .'ERROR: '. $message . PHP_EOL );
        }
        
        
        public function get_list_of_files_recursive ($folder) {
            $return = array();
            
            $folder = rtrim($folder, '/').'/';
            if(is_dir($folder)){
                $list = scandir($folder);
                foreach ($list as $oneFile) {
                    if (!preg_match('/^\./', $oneFile)) {
                        /* 除掉那些点号开头的文件 */
                        if (is_dir($folder. $oneFile)) {
                            $sub_files = $this->get_list_of_files_recursive($folder.$oneFile);
                            $return = array_merge($return, $sub_files);
                        } else {
                            $return[] = $folder . $oneFile;
                        }
                    }
                }
            }
            
            return $return;
        }
        
        
        public function merge () {
            $files = $this->get_list_of_files_recursive($this->from);
            $counts = count($files);
            if(!$counts){
                $this->echoError('没有任何文件可合并');
            }
            
            foreach ($files as $k => $one) {
                /* 对比文件名 */
                $file_name = pathinfo($one, PATHINFO_BASENAME);
                $tobe = $this->to . $file_name;
                $file_ext = pathinfo($one, PATHINFO_EXTENSION);
                $file_name_pre = pathinfo($one, PATHINFO_FILENAME);
                
                if (!in_array($file_name, $this->merged_list)) {
                    /* 不重复 */
                    $this->merged_list[] = $file_name;
                    copy($one, $tobe); // 复制
                    unlink($one);  // 删除
                    
                    
                } else {
                    $this->log('Repeat: '.$file_name.' in '.$one);
                    
                    /* 重复文件 */
                    $zuo = SYMBOL_LEFT;
                    $you = SYMBOL_RIGHT;
                    
                    if (!key_exists($file_name, $this->special_cases)) {
                        /* 新猪肉 */
                        $this->special_cases[$file_name] = 1;
                        
                    } else {
                        /* 旧猪肉 */
                        $lastNum = $this->special_cases[$file_name];
                        $newNum = $lastNum + 1;
                        $this->special_cases[$file_name] = $newNum;
                    }
                    
                    $repeat_num = $this->special_cases[$file_name];
                    $file_name_new = $file_name_pre.$zuo.$repeat_num.$you.'.'.$file_ext;
                    
                    $this->log('Repeat: '.$file_name.' [Repeat] '.$repeat_num);
                    $this->log('Repeat: '.$file_name.' [To Be] '.$file_name_new);
                    
                    while (in_array($file_name_new, $this->merged_list)) {
                        $this->log('Repeat: '.$file_name.' [Exists in Target folder] yes');
                        
                        $repeat_num++;
                        $this->special_cases[$file_name] = $repeat_num;
                        $file_name_new = $file_name_pre.$zuo.$repeat_num.$you.'.'.$file_ext;
                        
                        $this->log('Repeat: '.$file_name.' [New Repeat] '.$repeat_num);
                        $this->log('Repeat: '.$file_name.' [New To Be] '.$file_name_new);
                    }
                    
                    $this->merged_list[] = $file_name_new;
                    copy($one, $this->to . $file_name_new); // 复制
                    unlink($one);  // 删除
                    
                    $this->log('Repeat: '.$file_name.' [Done] ');
                }
            }
            
        }
        
        public function log ($mess) {
            if ($this->need_log) {
                echo PHP_EOL.'[DEV] '.$mess.PHP_EOL;
            }
        }
        
        public function echoResult () {
            die( PHP_EOL .'成功！其中重复文件共有'. count($this->special_cases) .'组' . PHP_EOL );
        }
        
    } /* End of CLASS */
    
    
    
    

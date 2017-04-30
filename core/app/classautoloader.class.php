<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco                                      |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or (at your option) any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
 */

class ClassAutoLoader
{

    private $paths;
    private $exclusion;

  // ==================================================================================
  // Function: 	    __construct()
  // Parameters:    none
  // Return:
  // ==================================================================================

    public function __construct()
    {
        $this->paths         = array();
        $this->exclusion     = array();
    
        spl_autoload_register(array($this, 'Load_Class'), true);
        spl_autoload_register(array($this, 'Load_Models'), true);
    }

  // ==================================================================================
  // Function: 	    add_Path()
  // Parameters:    $pathname
  // Return:
  // ==================================================================================

    public function add_Path($pathname)
    {
        // Scan and add all subfolders
        if (file_exists($pathname)) {
            $this->paths = array_merge($this->paths, $this->scan_Path($pathname));
        }
    }
  
  // ==================================================================================
  // Function: 	    add_Exclusion()
  // Parameters:    $path
  // Return:
  // ==================================================================================

    public function add_Exclusion($path)
    {
        $this->exclusion[] = $path;
    }
  
  // ==================================================================================
  // Function: 	    Scan_Path()
  // Parameters:    $path
  // Return:	    array with containing folder and subfolder(s)
  // ==================================================================================

    public function scan_Path($path)
    {
        $cf   = null;
        $cf[] = $path;
    
        foreach (glob($path.'/*', GLOB_ONLYDIR) as $dir) {
            foreach ($this->scan_Path($dir) as $sf) {
                if (!in_array($sf, $this->exclusion)) {
                    $cf[] = $sf;
                }
            }
        }
        return $cf;
    }

  // ==================================================================================
  // Function: 	    Load_Class()
  // Parameters:    $classname
  // Return:
  // ==================================================================================

    private function Load_Class($classname)
    {
        foreach ($this->paths as $dir) {
            $file_full_path = $dir . '/' . $classname . '.class.php';
    
            if (file_exists($file_full_path)) {
                include($file_full_path);
            }
        
            if (file_exists(strtolower($file_full_path))) {
                include(strtolower($file_full_path));
            }
        }
    }
  
  // ==================================================================================
  // Function: 	    Load_Models()
  // Parameters:    $classname
  // Return:
  // ==================================================================================

    public function Load_Models($classname)
    {
        foreach ($this->paths as $dir) {
            list($class) = explode('_', $classname);
            $file_full_path = $dir . '/' . $class . '.model.php';
            $file_full_path = strtolower($file_full_path);
            
            if (file_exists($file_full_path)) {
                include($file_full_path);
            }
        }
    }
} // end class ClassAutoLoader

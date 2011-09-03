<?php

Class Home_Default_Controller {
    
    function __construct()
    {

    }
    
    // sitename.com/home/default/main
    // aka: sitename.com/MODULE/CONTROLLER/METHOD/VAR1/VAR2
    function main()
    {
        $data = array();
        
        // load models by module. better re-usability
        // modules/home/models/default.model.php
        $model = Loader::loadModel('home','default');
        $data['var'] = $model->test('TESTING', array('foo','bar'));
        
        // assign $data and load the view from modules/home/views/default.view.php
        // Loader::loadView(MODULE, VIEW, (array)$VARS);
        $view = Loader::loadView('home','default',$data);
        
        // simple assignment.
        // Note: use Registry class
        $content = 'Here\'s some more text.';
        
        // assign $content to a default layout
        // Loader::loadLayout(LAYOUT, (array)$VARS);
        $layout = Loader::loadLayout(
            'default',
            array(
                'view' => $view,
                'content' => $content
            )
        );
        
        // return it. echoed in html/index.php 
        return $layout;
    }
    
    // sitename.com/home/default/something
    // try: sitename.com/home/default/something/foo/bar
    public function something($var1='var1', $var2='var2')
    {
        die($var1.' - '.$var2);
    }
    
    // sitename.com/home/default/nothing
    // not accessible
    private function nothing()
    {
        die(__METHOD__);
    }
    
}
<?php 
require_once("../vendor/autoload.php");
require_once("header.html");

//print "Hello!";

?>
<h2>&nbsp;</h2>
<div class="container">
    <div class="row">
        <div class="col-sm-2">
        <h2>Speaker</h2>
        </div>
        <div class="col-sm-10">
            <div id="ready_message"></div>
        </div>    
        </div>
</div>        
<div class="container">
    <div class="row">

        <div class="col-xs-2">
            <p>Select point:</p>
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-light">
                    <input type="radio" name="pointer" id="start" autocomplete="off" > START 
                </label>
                <label class="btn btn-light">
                    <input type="radio" name="pointer" id="end" autocomplete="off" > END
                </label>
            </div>
            <ul id="coordinates"><li>start: 0</li><li> end: 0</li></ul>
            
        </div>
        <div class="col-sm-2">&nbsp;</div>
       
        
          <div class="col-sm-4">  
          <p>Or choose duration of fragment</p>
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-outline-secondary">
                    <input type="radio" name="duration" id="duration50" autocomplete="off" > 50ms
                </label>
                <label class="btn btn-outline-secondary">
                    <input type="radio" name="duration" id="duration100" autocomplete="off"> 100ms
                </label>
                <label class="btn btn-outline-secondary">             
                    <input type="radio" name="duration" id="duration500" autocomplete="off"> 500ms           
                </label>
                <label class="btn btn-outline-secondary">             
                    <input type="radio" name="duration" id="duration1000" autocomplete="off"> 1000ms           
                </label>
            </div>
                        
        </div>
        <div class="col-sm-2" id="current_position">
            <ul>
               <li>x = 0</li>
               <li>fn = none</li>
             </ul>
        </div>                      
        
    </div>
</div>

<div class="container" id="work_area"> 
    <div class="row">
        <div class="col-sm-12">
            <img src="assets/i/h-w1.jpg" title="h-w1.jpg" />
        </div>
    </div>          
</div>

<div class="container">
<div class="row">
    <div class="col-sm-2">
        <div class="btn btn-warning" id="run">RUN</div>
    </div>
</div>
<div class="row">

 <div class="col-sm-2">
             <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-light">
                    <input type="radio" name="res" id="res-power" autocomplete="off" > POWER 
                </label>
                <label class="btn btn-light">
                    <input type="radio" name="res" id="res-dots" autocomplete="off" > DOTS
                </label>
                <label class="btn btn-light">
                    <input type="radio" name="res" id="res-wfft" autocomplete="off" > Windowed FFT
                </label>
                <label class="btn btn-light">
                    <input type="radio" name="res" id="res-wt" autocomplete="off" > Wavelet Analysis
                </label>
            </div>
        </div>
        </div>
        </div> 
<?php
require_once("footer.html");

//	exec("/usr/bin/python ./index.py", $a);
//print_r($a);

<style type="text/css">
    ul.dropdown-menu {
    background-color: #911141;
    width: 100%;
    height: auto;
    padding: 8px 20px;
    border-top: 1px solid rgba(255,255,255,0.2);
    border-bottom: 0px solid rgba(0,0,0,0.15);
    position: relative;
}
.dropdown-menu>li>a {
    display: block;
    padding: 8px 0px;
    clear: both;
    font-weight: 400;
    line-height: 1.42857143;

    color: #333;
    white-space: nowrap;
}
.dropdown-menu>li>a:hover, .dropdown-menu>li>a:focus {
    background-color: rgb(145, 17, 65);
    color: #fff;
}
.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    display: none;
    float: left;
    min-width: 160px;
    padding: 5px 0;
    margin: 2px 0 0;
    font-size: 14px;
    text-align: left;
    list-style: none;
    background-color: #fff;
    -webkit-background-clip: padding-box;
    background-clip: padding-box;
    border: 1px solid #ccc;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 4px;
    -webkit-box-shadow: 0 0px 0px rgba(0,0,0,.175); */
     box-shadow: 0 0px 0px rgba(0,0,0,.175); */
}
</style>

<div class="content">

    <div class="container">

        <div class="vd_nav-width vd_navbar-tabs-menu vd_navbar-left" style="color:black;background-color:#911141;">

            <!--            <div class="navbar-tabs-menu clearfix">

                        </div>-->

            <div class="navbar-menu clearfix">

                <!--                <div class="vd_panel-menu hidden-xs">

                                    <span data-original-title="Expand All" data-toggle="tooltip" data-placement="bottom" data-action="expand-all" class="menu" data-intro="<strong>Expand Button</strong><br/>To expand all menu on left navigation menu." data-step=4 >

                                        <i class="fa fa-sort-amount-asc"></i>

                                    </span>                   

                                </div>-->

                <h3 class="menu-title hide-nav-medium hide-nav-small"></h3>

                <div class="vd_menu">



                    <ul>

                 <!--        <li>

                            <a style="color:black" href="<?php //echo $this->config->base_url() ?>admin/index">

                                <span class="menu-icon"><i class="fa fa-map-marker" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white" >Map</span>  



                            </a>

                        </li> -->

                        <li>

                            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/users">

                                <span class="menu-icon"><i class="fa fa-users" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Users</span>  



                            </a>

                        </li>

                       <!--  <li>

                            <a  style="color:black" href="<?php //echo $this->config->base_url() ?>admin/drivers">

                                <span class="menu-icon"><i class="fa fa-user" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Drivers</span>  



                            </a>

                        </li> -->

                   <!--      <li>

                            <a  style="color:black" href="<?php //echo $this->config->base_url() ?>admin/Ambulance">

                                <span class="menu-icon"><i class="fa fa-user" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">AMBULANCE</span>  



                            </a>

                        </li>

                        <li>

                            <a  style="color:black" href="<?php //echo $this->config->base_url() ?>admin/Doctor">

                                <span class="menu-icon"><i class="fa fa-user" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">DOCTOR</span>  



                            </a>

                        </li>

                        <li>

                            <a  style="color:black" href="<?php// echo $this->config->base_url() ?>admin/Nurse">

                                <span class="menu-icon"><i class="fa fa-user" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">NURSE</span>  



                            </a>

                        </li> -->
                            <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"> <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span>Ambulance <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                        <li>

                            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/Ambulance">

                                <span class="menu-icon"><i class="fa fa-user" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Ambulance user</span>  



                            </a>

                        </li>
                         <li>

                            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/Ambulance_getrides">

                                <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Ambulance Rides</span>  



                            </a>

                        </li>

                    </ul>
                </li>
                <!--<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"> <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span>Doctor <span class="caret"></span></a>-->
                <!--            <ul class="dropdown-menu">-->
                <!--           <li>-->

                <!--            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/Doctor">-->

                <!--                <span class="menu-icon"><i class="fa fa-user" style="color:white"></i></span> -->

                <!--                <span class="menu-text" style="color:white">Doctor user</span>  -->



                <!--            </a>-->

                <!--        </li>-->
                <!--   <li>-->

                <!--            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/Doctor_getrides">-->

                <!--                <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span> -->

                <!--                <span class="menu-text" style="color:white"> Doctor Visits</span>  -->



                <!--            </a>-->

                <!--        </li>-->

                <!--    </ul>-->
                <!--</li>-->
                <!--  <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"> <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span>Nurse <span class="caret"></span></a>-->
                <!--            <ul class="dropdown-menu">-->
                <!--               <li>-->

                <!--            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/Nurse">-->

                <!--                <span class="menu-icon"><i class="fa fa-user" style="color:white"></i></span> -->

                <!--                <span class="menu-text" style="color:white">Nurse user</span>  -->



                <!--            </a>-->

                <!--        </li>-->
                <!--   <li>-->

                <!--            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/Nurse_getrides">-->

                <!--                <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span> -->

                <!--                <span class="menu-text" style="color:white"> Nurse Visits</span>  -->



                <!--            </a>-->

                <!--        </li>-->
                <!--    </ul>-->
                <!--</li>-->
                         <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"> <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span>Master <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                    <!--     <li>

                            <a  style="color:black" href="<?php //echo $this->config->base_url() ?>admin/addType">

                                <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Add Category</span>  



                            </a>

                        </li> -->

                        <li>

                            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/addSubType">

                                <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Add Subcategory</span>  



                            </a>

                        </li>
                    </ul>
                </li>
                 <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"> <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span>Commission Master <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                        <li>

                            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/add_comm">

                                <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Add Commission</span>  



                            </a>

                        </li>

                       <!--  <li>

                            <a  style="color:black" href="<?php //echo $this->config->base_url() ?>admin/view_comm">

                                <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">View Commission</span>  



                            </a>

                        </li> -->
                    </ul>
                </li>
                 <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"> <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span>Setting <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                             <li>

                            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/settings">

                                <span class="menu-icon"><i class="fa fa-gears" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white"> Default settings</span>  



                            </a>

                        </li>
                  <!--          <li>

                            <a style="color:black" href="<?php// echo $this->config->base_url() ?>admin/amb_select">

                                <span class="menu-icon"><i class="fa fa-gears" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Ambulance settings</span>  



                            </a>

                        </li> -->


                      <!--   <li>

                            <a style="color:black" href="<?php //echo $this->config->base_url() ?>admin/Doc_select">

                                <span class="menu-icon"><i class="fa fa-gears" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Doctor settings</span>  



                            </a>

                        </li> -->
                       <!--    <li>

                            <a style="color:black" href="<?php //echo $this->config->base_url() ?>admin/Nur_select">

                                <span class="menu-icon"><i class="fa fa-gears" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Nurse settings</span>  



                            </a>

                        </li> -->
                    </ul>
                </li>
                         <li>

                            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/Earnings">

                                <span class="menu-icon"><i class="fa fa-users" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Earnings</span>  



                            </a>

                        </li>


                      <!--   <li>

                            <a  style="color:black" href="<?php// echo $this->config->base_url() ?>admin/getrides">

                                <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white"> Rides</span>  



                            </a>

                        </li> -->

                        <!-- <li>

                            <a  style="color:black" href="<?php// echo $this->config->base_url() ?>admin/Ambulance_getrides">

                                <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Ambulance Rides</span>  



                            </a>

                        </li>

                        <li>

                            <a  style="color:black" href="<?php //echo $this->config->base_url() ?>admin/Doctor_getrides">
//
                                <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white"> Doctor Rides</span>  



                            </a>

                        </li>

                        <li>

                            <a  style="color:black" href="<?php// echo $this->config->base_url() ?>admin/Nurse_getrides">

                                <span class="menu-icon"><i class="fa fa-taxi" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white"> Nurse Rides</span>  



                            </a>

                        </li>

                        
 -->
<!--  <li>
                            <a  style="color:black" href="<?php //echo $this->config->base_url() ?>admin/getPayments">

                                <span class="menu-icon"><i class="fa fa-suitcase" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Payments</span>  



                            </a>

                        </li> -->

                  
                     

                        <li>

                            <a  style="color:black" href="<?php echo $this->config->base_url() ?>admin/logout">

                                <span class="menu-icon"><i class="fa fa-sign-out" style="color:white"></i></span> 

                                <span class="menu-text" style="color:white">Logout</span>  



                            </a>

                        </li>

                    </ul>

                </div>

            </div>

            <div class="navbar-spacing clearfix">

            </div>

        </div>




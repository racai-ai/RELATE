<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <title>RELATE
    </title>
    <style>#loader{transition:all .3s ease-in-out;opacity:1;visibility:visible;position:fixed;height:100vh;width:100%;background:#fff;z-index:90000}#loader.fadeOut{opacity:0;visibility:hidden}.spinner{width:40px;height:40px;position:absolute;top:calc(50% - 20px);left:calc(50% - 20px);background-color:#333;border-radius:100%;-webkit-animation:sk-scaleout 1s infinite ease-in-out;animation:sk-scaleout 1s infinite ease-in-out}@-webkit-keyframes sk-scaleout{0%{-webkit-transform:scale(0)}100%{-webkit-transform:scale(1);opacity:0}}@keyframes sk-scaleout{0%{-webkit-transform:scale(0);transform:scale(0)}100%{-webkit-transform:scale(1);transform:scale(1);opacity:0}}
    </style>
    <link href="extern/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css" />    
    <link href="style.css" rel="stylesheet" type="text/css" />
    <link href="vis.min.css" rel="stylesheet" type="text/css" />

    <?php
    if(function_exists("getPageAdditionalCSS")){
        $css=getPageAdditionalCSS();
        foreach($css as $link){
            ?><link href="<?php echo $link;?>" rel="stylesheet" type="text/css" /><?php
        }
    }
    ?>

    <style>
    <?php echo getPageCSS(); ?>
    </style>
  </head>
  <body class="app">
    <div id="loader">
      <div class="spinner">
      </div>
    </div>
<script>

window.addEventListener('load', () => {
        const loader = document.getElementById('loader');
        setTimeout(() => {
          loader.classList.add('fadeOut');
        }, 300);
        
        if(document.getElementsByClassName("sidebar")[0].clientWidth<100){
            document.getElementsByTagName("body")[0].className="app is-collapsed";
        }
        
        var el=document.getElementById("menuActive");
        if(el!==undefined && el!==null)el.setAttribute("class","sidebar-link active");
        
      });</script>
    <div>
      <div class="sidebar">
        <div class="sidebar-inner">
          <div class="sidebar-logo">
            <div class="peers ai-c fxw-nw">
              <div class="peer peer-greed">
                <a class="sidebar-link td-n" href="index.php">
                  <div class="peers ai-c fxw-nw">
                    <div class="peer">
                      <div class="logo">
                        <img src="assets/static/images/logo.png" alt="">
                      </div>
                    </div>
                    <div class="peer peer-greed">
                      <h5 class="lh-1 mB-0 logo-text">RELATE</h5>
                    </div>
                  </div></a>
              </div>
              <div class="peer">
                <div class="mobile-toggle sidebar-toggle">
                  <a href="" class="td-n">
                    <i class="ti-arrow-circle-left"></i></a>
                </div>
              </div>
            </div>
          </div>
          <ul class="sidebar-menu scrollable pos-r">
            <?php foreach($PLATFORM['menu'] as $menu){
                    if(isset($menu['rights']) && !$user->hasAccess($menu['rights']))continue;
                    $isOpen="";
                    if(isset($menu['menu'])){
                        foreach($menu['menu'] as $menu2){
                            if($menu2['path']==$PLATFORM['path']){
                                $isOpen="open";
                                break;
                            }
                        }
                    }
             ?>
            <li class="nav-item dropdown <?php echo $isOpen; ?>">
              <a class="dropdown-toggle" href="<?php echo makeMenuLink($menu); ?>">
                <span class="icon-holder"><?php echo isset($menu['icon'])?"<i class=\"${menu['icon']}\"></i>":""; ?></span>
                <span class="title"><?php echo $menu['label'];?></span> 
                <?php if(isset($menu['menu'])){ ?>
                    <span class="arrow"><i class="ti-angle-right"></i></span></a>
                    <ul class="dropdown-menu">

                    <?php foreach($menu['menu'] as $menu2){ 
                            if(isset($menu2['rights']) && !$user->hasAccess($menu2['rights']))continue;
                            $idMenuActive="";
                            if(isset($menu2['path']) && $PLATFORM['path']==$menu2['path'])$idMenuActive="id=\"menuActive\"";
                    ?>
                        <li><a class="sidebar-link" <?php echo $idMenuActive; ?> href="<?php echo makeMenuLink($menu2); ?>"><?php echo htmlspecialchars($menu2['label']);?></a></li>
                    <?php } ?>
                    
                    </ul>
                <?php } ?>
            </li>
            <?php } ?>

          </ul>


        </div>
      </div>
      <div class="page-container">
        <div class="header navbar">
          <div class="header-container">
            <ul class="nav-left">
              <li>
              <a id="sidebar-toggle" class="sidebar-toggle" href="javascript:void(0);">
                <i class="ti-menu"></i></a>
              </li>
              
              <li><a>Romanian Portal of Language Technologies</a></li>
            </ul>

            <ul class="nav-right">
            <?php if($user->isLoggedIn()){ ?>
            <li class="dropdown">
                <a href="" class="dropdown-toggle no-after peers fxw-nw ai-c lh-1" data-toggle="dropdown" aria-expanded="false">
                    <div class="peer"><span class="fsz-sm c-grey-900">Welcome</span></div>
                    <div class="peer mR-10"><img class="w-2r bdrs-50p" src="user_icon_2.svg" alt=""></div>
                    <div class="peer"><span class="fsz-sm c-grey-900"><?php echo $user->getProfileHtml('name','Unknown');?></span></div>
                </a>
                <ul class="dropdown-menu fsz-sm">
                    <li><a href="<?php echo makeLink("platform/change_password");?>" class="d-b td-n pY-5 bgcH-grey-100 c-grey-700"><i class="ti-settings mR-10"></i> <span>Change password</span></a></li>
                    <!--
                    <li><a href="" class="d-b td-n pY-5 bgcH-grey-100 c-grey-700"><i class="ti-user mR-10"></i> <span>Profile</span></a></li>
                    <li><a href="email.html" class="d-b td-n pY-5 bgcH-grey-100 c-grey-700"><i class="ti-email mR-10"></i> <span>Messages</span></a></li>
                    -->
                    <li role="separator" class="divider"></li>
                    <li><a href="<?php echo makeLink("platform/logout");?>" class="d-b td-n pY-5 bgcH-grey-100 c-grey-700"><i class="ti-power-off mR-10"></i> <span>Logout</span></a></li>
                </ul>
            </li>
            <?php } else { ?>
                <li><a href="<?php echo makeLink("platform/login");?>">Login</a></li>
              
            <?php } ?>
            
            </ul>
          </div>
        </div>


        <!-- ### $App Screen Content ### -->
        <main class='main-content bgc-grey-100' style="min-height: calc(100vh - 61px);">
          <div id='mainContent' style="    min-height: calc(100vh - 151px);">
            <div class="row gap-20 masonry pos-r" style="    min-height: calc(100vh - 151px);">
              <div class="masonry-sizer col-md-6"></div>

              <?php echo getPageContent();?>

            </div>
          </div>
        </main>

        <footer class="bdT ta-c p-30 lh-0 fsz-sm c-grey-600">
          <span>Copyright © RACAI. All rights reserved.
          </span>
        </footer>
      </div>
    </div>
<script type="text/javascript" src="vendor.js"></script>
<script type="text/javascript" src="bundle.js"></script>
<script src="jquery-3.2.1.min.js"></script>
<script src="jquery.sparkline.min.js"></script>
<script src="extern/jquery.ui.touch-punch.min.js"></script>
<script src="extern/jquery-ui-1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="vis.min.js"></script>

    <?php
    if(function_exists("getPageAdditionalJS")){
        $js=getPageAdditionalJS();
        foreach($js as $link){
            ?><script type="text/javascript" src="<?php echo $link;?>"></script><?php
        }
    }
    ?>


<script>
<?php echo getPageJS(); ?>
</script>
  </body>
</html>
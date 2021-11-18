<main class="homepage">
      <div class="lang-selection">
        
        <div class="dropup">
          <button class="dropdown-toggle btn-lang" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <?php echo trans('language'); ?><span class="flag is-selected">
                <img src="web/public/assets/img/<?php echo $lang; ?>.png" width="24px">
              </span>
            <!-- <span class="caret"></span> -->
          </button>
          <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
            <li><a href="?_act_=lang&lang=en&return=index"><img src="web/public/assets/img/en.png" width="24px"> English</a></li>
            <li><a href="?_act_=lang&lang=de&return=index"><img src="web/public/assets/img/de.png" width="24px"> Deutsch</a></li>
            <li><a href="?_act_=lang&lang=fr&return=index"><img src="web/public/assets/img/fr.png" width="24px"> Français</a></li>
            <li><a href="?_act_=lang&lang=it&return=index"><img src="web/public/assets/img/it.png" width="24px"> Italiano</a></li>
          </ul>
        </div>
      </div>
      <div class="hompage-content">
        <div class="logo">
          <img src="web/public/assets/img/logo.png" width="80">
        </div>
        <!-- TOTO Fix inline css -->
        <a href="https://open.publibike.ch/app" style="color: white; text-decoration: none;">
        <div class="menu">
          <div class="menu-item bg-pink">
            <div class="menu-item_icon">
				<img src="web/public/assets/img/rent-bike-btn.png" width="85">
            </div>
            <div class="menu-item_text">
                <?php echo trans('download');?>
            </div>
          </div>
        </a>
        <!-- TOTO Fix inline css -->
          <a href="?_act_=report" style="color: white; text-decoration: none;">
          <div class="menu-item bg-blue">
            <div class="menu-item_icon">
			<img src="web/public/assets/img/report-bike-btn1.png" width="65">
              <!-- <svg width="45px" height="50px" viewBox="0 0 45 50" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <g id="Welcome" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="Home" transform="translate(-255.000000, -514.000000)" fill="#EBEBEB" fill-rule="nonzero">
                        <g id="Button" transform="translate(202.000000, 480.000000)">
                            <g id="location" transform="translate(53.000000, 34.000000)">
                                <path d="M28.9827731,32.9256303 C29.0798319,32.7676471 29.1768908,32.612605 29.2739496,32.4516807 C33.6390756,25.2247899 35.8357143,18.9029412 35.8042017,13.6638655 C35.7638458,6.16015836 29.6701894,0.0981593273 22.1663738,0.0968878231 C14.6625582,0.0956167188 8.56684805,6.15555061 8.52394958,13.6592437 C8.49201681,18.9029412 10.6890756,25.2256303 15.0542017,32.4516807 C15.1512605,32.612605 15.2483193,32.7676471 15.3453782,32.9256303 C6.15546218,34.0613445 0.0630252101,37.3457983 0.0630252101,41.2453782 C0.0630252101,46.1411765 9.76890756,49.9760504 22.1638655,49.9760504 C34.5588235,49.9760504 44.2647059,46.1411765 44.2647059,41.2453782 C44.2647059,37.3457983 38.1722689,34.0613445 28.9827731,32.9256303 Z M9.99453782,13.6638655 C9.96973291,9.29907679 12.2843792,5.25527664 16.0605944,3.06616838 C19.8368096,0.877060123 24.4961987,0.877944914 28.2715822,3.06848718 C32.0469657,5.25902944 34.360076,9.30370838 34.3336134,13.6684874 C34.3966387,24.1130252 24.6319328,36.994958 22.1638655,40.0840336 C19.6957983,36.994958 9.93067227,24.112605 9.99453782,13.6638655 L9.99453782,13.6638655 Z M22.1638655,48.5054622 C9.82310924,48.5054622 1.53361345,44.7512605 1.53361345,41.2453782 C1.53361345,38.1361345 7.6802521,35.2432773 16.2117647,34.305042 C17.8618023,36.8813845 19.6622635,39.3582288 21.6037815,41.7226891 C21.7433293,41.8871957 21.9481435,41.9820342 22.1638655,41.9820342 C22.3795876,41.9820342 22.5844018,41.8871957 22.7239496,41.7226891 C24.6656114,39.35824 26.4662131,36.8813955 28.1163866,34.305042 C36.6457983,35.2432773 42.7945378,38.1344538 42.7945378,41.2453782 C42.7941176,44.7512605 34.5046218,48.5054622 22.1638655,48.5054622 L22.1638655,48.5054622 Z" id="Shape"></path>
                                <path d="M28.3726891,13.6638655 C28.372225,10.2346497 25.591961,7.4550755 22.1627452,7.45546224 C18.7335293,7.45584898 15.9538924,10.2360502 15.9542018,13.6652661 C15.9545111,17.094482 18.7346497,19.8741816 22.1638655,19.8739496 C25.591668,19.869781 28.3692163,17.0916687 28.3726891,13.6638655 L28.3726891,13.6638655 Z M17.4252101,13.6638655 C17.4256742,11.0468878 19.5474479,8.92574108 22.1644257,8.92605045 C24.7814034,8.92635983 26.9026756,11.0480082 26.9025209,13.6649859 C26.9023663,16.2819637 24.7808433,18.4033613 22.1638655,18.4033613 C19.5476026,18.4005816 17.4275259,16.2801289 17.4252101,13.6638655 Z" id="Shape"></path>
                            </g>
                        </g>
                    </g>
                </g>
            </svg> -->
            </div>
            
            <div class="menu-item_text">
                <?php echo trans('report_a_lost_bike'); ?>
            </div>
          </div>
            </a>
        </div>
		
		<div class="menu">
			<!-- TOTO Fix inline css -->
			<a href="<?php echo trans('offerurl'); ?>" style="color: white; text-decoration: none;">
				<div class="menu-item bg-pink">
					<div class="menu-item_icon">
						<img src="web/public/assets/img/offers-btn.png" width="85">
					</div>
				
					<div class="menu-item_text">
						<?php echo trans('offers'); ?>
					</div>
				</div>
			</a>
			
			<!-- TOTO Fix inline css -->
			<a href="<?php echo trans('infourl'); ?>" style="color: white; text-decoration: none;">
				<div class="menu-item bg-blue">
					<div class="menu-item_icon">
						<img src="web/public/assets/img/info-btn.png" width="85">
					</div>
				
					<div class="menu-item_text">
						<?php echo trans('infos'); ?>
					</div>
				</div>
			</a>
		</div>
      </div>
    </main>

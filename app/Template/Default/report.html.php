<main>
  <div style="height:300px" id="map"></div>
  <div id="report-page"></div>
      <input id="form-controller" type="checkbox" checked>
        <label for="form-controller" class="form-controller"></label>
      <div class="box">
        <!-- <div>
          <a href="/?_act_=lang&lang=de&return=report">DE</a> &nbsp; &nbsp; || &nbsp;&nbsp;
          <a href="/?_act_=lang&lang=en&return=report">EN</a>
        </div> -->
        <div class="form-finding">
          <div class="location">
            <div class="location-icon">
                    <i
                    style="font-size:24px;"
                    class="fas fa-map-marker-alt"
                  ></i
                >
            </div>
            <div class="location-value">
              <div id="current-address"></div>
              <div class="help-text" id="coordinates"></div>
              <p class="location-value__get">
                    <a href="javascript: getCurrentLocation();" class="text-blue"><strong><?php echo trans('my_loction'); ?></strong></a>
              </p>
            </div>
             <p id="info" class="location-error"></p>
          </div>
          <div class="form-label-group">
            <textarea
              id="inputComment"
              class="form-control"
              rows="2"
              placeholder="<?php echo trans('comment')?>"
            ></textarea>
          </div>
          <div class="form-label-group">
            <input
              id="inputBikeID"
              class="form-control"
              placeholder="<?php echo trans('lost_bike_id'); ?>"
              required=""
            />
            <p class="help-text">
            <?php echo trans('bike_number_on_lock_screen'); ?>
            </p>
          </div>

          <div class="hidden-parts">
            <div class="form-label-group">
              <input
                type="email"
                id="inputEmail"
                class="form-control"
                placeholder="<?php echo trans('email_addess')?>"
                required=""
              />
            </div>

            <div class="form-label-group">
              <label for="inputPhoneNumber"></label>
              <input
                type="number"
                id="inputP honeNumber"
                class="form-control"
                required=""
                placeholder="<?php echo trans('phone_number')?>"
              />
            </div>

            

            <div class="text-center mb-4">
				<div class="gallery-image">
					<div id="img-wrap" style="display: none;" class="img-wrap mb-4">
						<a href="javascript:unloadImage();">
						<span class="close">&times;</span>
						</a>
						<img id="image-display" class="mb-4 form-finding-img" alt="" width="160" height="160" src="https://gamek.mediacdn.vn/133514250583805952/2021/5/6/photo-1-1620285346044255144007.jpg">
					</div>
				</div>
                <!-- <img id="image-display" style="display: none;" class="mb-4 form-finding-img" alt="" width="160" height="160"> -->
              <div class="form-finding-img-box">
                  
                <input
                  style="display:none"
                  id="image"
                  type="file"
                  name="image[]"
                  accept="image/*"
                  capture="camera"
				  multiple
                />
                <a href="javascript:document.getElementById('image').click();"
                  ><i
                    style="font-size:24px;"
                    class="fas fa-camera new-photo"
                  ></i
                ></a>
              </div>
	
            </div>
          </div>
          <p id="normal-text" class="text normalmsg"><?php echo trans('normal_text')?></p>
          <div class="box-action">
            <button class="btn btn-lg btn-block btn-primary btn-center" id="upload" type="submit">
            <?php echo trans('send')?>
            </button>
          </div>
        </div>
      </div>
      </div>
      <div id="thankyou-page" style="display: none;">
      <input id="form-controller" type="checkbox" checked />
      <label for="form-controller" class="form-controller invisible"></label>
      <div class="box">
        <div class="form-finding">
          <div class="thankyou">
            <div class="thankyou-sign">
              <svg
                width="120px"
                height="120px"
                viewBox="0 0 120 120"
                version="1.1"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
              >
                <g
                  id="Welcome"
                  stroke="none"
                  stroke-width="1"
                  fill="none"
                  fill-rule="evenodd"
                >
                  <g
                    id="Report-Success"
                    transform="translate(-128.000000, -253.000000)"
                    fill="#00B5D1"
                    fill-rule="nonzero"
                  >
                    <g
                      id="accept"
                      transform="translate(128.000000, 253.000000)"
                    >
                      <path
                        d="M60.0005225,0 C26.9152724,0 0,26.9145694 0,60 C0,93.0843854 26.9152724,120 60.0005225,120 C93.0847276,120 120,93.0843854 120,60 C120,26.9145694 93.0847276,0 60.0005225,0 Z M60.0005225,115.108247 C29.6135926,115.108247 4.89188077,90.385091 4.89188077,60 C4.89188077,29.6138638 29.6135926,4.89175295 60.0005225,4.89175295 C90.3864074,4.89175295 115.108119,29.6138638 115.108119,60 C115.108119,90.385091 90.3864074,115.108247 60.0005225,115.108247 Z"
                        id="Shape"
                      ></path>
                      <path
                        d="M90.8792082,36.2316867 L48.3663409,78.6141647 L29.1207918,59.4282109 C28.1689802,58.4792953 26.6267118,58.4792953 25.6738587,59.4282109 C24.7220471,60.3771264 24.7220471,61.9147022 25.6738587,62.8646562 L46.6428744,83.7688324 C47.1187804,84.2432904 47.7425605,84.48 48.3663409,84.48 C48.9901213,84.48 49.6139017,84.2432904 50.0898075,83.7688324 L94.3261413,39.668132 C95.2779529,38.7192162 95.2779529,37.1806025 94.3261413,36.2316867 C93.3743295,35.2827711 91.8310201,35.2827711 90.8792082,36.2316867 Z"
                        id="Path"
                      ></path>
                    </g>
                  </g>
                </g>
              </svg>
            </div>
            <h4 class="thankyou-title"><?php echo trans('thankyou_for_reporting'); ?></h4>
            <a href="https://publibike-service.ch/gefunden/">
            <button
              class="btn btn-lg btn-block btn-primary btn-center"
              type="submit"
            >
            <?php echo trans('download_the_app'); ?>
            </button>
            </a>
          </div>
        </div>
      </div>
      </div>
    </main>
    <div class="loading" style="display:none">lädt...&#8230;</div>

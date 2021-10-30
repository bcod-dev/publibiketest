var map, infoWindow, geocoder;

var pos = {
    lat: null,
    lng: null
}
var googleMapLink = null;

//Check current permission
if(navigator.permissions != undefined){
    navigator.permissions.query({name:'geolocation'})
        .then(function(permissionStatus) {
            console.log(permissionStatus); //permissionStatus.state = prompt/granted/denied
            switch(permissionStatus.state) {
                case 'granted':
                    onGranted();
                    break;
                case 'prompt':
                    onPrompt();
                    break;
                    onNoPermission();
                default: 
            }
        permissionStatus.onchange = function() {
            console.log('geolocation permission state has changed to ', this.state);

            switch(permissionStatus.state) {
                case 'granted':
                    onChangedToGranted();
                    break;
                case 'prompt':
                    onPrompt();
                    break;
                    onNoPermission();
                default: 
            }
        };
    });
} else {
    console.log('navigator.permission is undefined');
    //showError('Getting location is not supported, please enter the address in comment!');
}


function onGranted(){ //For first time check
    console.log("onGranted");
    getCurrentLocation();
}

function onChangedToGranted(){
    console.log("onChangedToGranted");
}

function onPrompt(){
    console.log('onPrompt');
}

function onNoPermission(){
    console.log('onNoPermission');
    showError('no_location_permission_use_comment');
}
        

function initMap(){
    map = new google.maps.Map(document.getElementById('map'), {
    //center: {lat: -34.397, lng: 150.644},
    zoom: 2,
    panControl: true,
        zoomControl: false,
        mapTypeControl: false,
        scaleControl: false,
        streetViewControl: false,
        overviewMapControl: false,
        rotateControl: false
    });
    infoWindow = new google.maps.InfoWindow();
    geocoder = new google.maps.Geocoder();
    var berlin = {
        lat: 52.520008,
        lng: 13.404954
    }

    map.setCenter(berlin);
}
function onFinishGetLocation(){
    $('.loading').hide();
}

function getCurrentLocation() {
// Try HTML5 geolocation.
    $('.loading').show();
    if (navigator.geolocation) {
        console.log('Debug - Getting location...')
        //The callback of following function may never be called, we should check the pos variable and return error after few seconds
        // setTimeout(function(){
        //     if(pos.lat == null || pos.lng == null){
        //         handleLocationError(true);
        //     }
        // }, 3000), //Found another way by adding option timeout to following function 
        navigator.geolocation.getCurrentPosition(function(position) {
            console.log('Debug - Getting location...navigator.geolocation.getCurrentPosition');
            pos.lat = position.coords.latitude;
            pos.lng = position.coords.longitude;
            googleMapLink = pos.lat != null && pos.lng != null ?
                "http://www.google.com/maps/place/" + pos.lat +"," + pos.lng : 
                null;

            //TODO only allow sending email when location found?

            infoWindow.setPosition(pos);
            infoWindow.setContent(getTranslatedMsg('your_location'));
            infoWindow.open(map);

            var centerPos = {
                lat: pos.lat - 250,
                lng: pos.lng
            }

            map.setCenter(centerPos);
            map.setZoom(16);

            //Get current address
            geocoder.geocode({ location: pos }, (results, status) => {
                if (status === "OK") {
                    if (results[0]) {
                        $('#current-address').text(results[0].formatted_address);
                        $('#coordinates').text(pos.lat + ', ' + pos.lng);
                    } else {
                        showError("cant_get_address");
                    }
                } else {
                    showError("cant_get_address");
                }
            });
            

            // var myLatlng = new google.maps.LatLng(centerPos.lat,centerPos.lng);
            // var marker2 = new google.maps.Marker({
            //     position: myLatlng,
            //     map: map,
            //     title: 'Hello World!'
            // });

            onFinishGetLocation();

        }, function(err) {
            console.log('Debug - getCurrentPosition error', err)
            handleLocationError(true);
        }, {timeout:3000}); //Code 3 if problem with browser
    } else {
        console.log('Debug - Browser doesnt support geolocation');
        // Browser doesn't support Geolocation
        handleLocationError(false);
    }
}

function handleLocationError(browserHasGeolocation) {
    console.log('handleLocationError');
    var msg = browserHasGeolocation ?
                            'location_doesnt_work' :
                            'browser_doesnt_support_location_service';

    showError(msg);
}

function getTranslatedMsg(msg)
{
    console.log('getTranslatedMsg', msg, _lang);
    translatedMsg = msg;
    if(typeof _langData[msg] != 'undefined' && typeof _langData[msg][_lang] != 'undefined'){
        translatedMsg = _langData[msg][_lang];
    }
    return translatedMsg;
}

function showError(msg){
    var translatedMsg = getTranslatedMsg(msg);
    console.log('Showing error: ', msg);
    $('.loading').hide();
    $('.alert').remove();
    if(msg){
        $('#info').addClass('text-danger').removeClass('text-success').text(translatedMsg);
    }
}

function showSuccess(msg){
    var translatedMsg = getTranslatedMsg(msg);
    console.log('Showing success: ', msg);
    $('.alert').remove();
    $('.loading').hide();
    if(msg){
        $('#info').addClass('text-success').removeClass('text-danger').text(translatedMsg);
    }
}



    





//Submit form
function clearForm(){
    //window.location = window.location; //Refresh the page
}

function onSuccess(){
    showSuccess('send_success_thankyou')
    $('.loading').hide();
    $('#report-page').hide();
    $('#thankyou-page').show();
    $('#form-controller').hide();
    $('.form-controller').hide(); 
    scrollTop();
    clearForm();
}

function onError(error){
    if(error != null && error != undefined){
        showError(error)
    } else {
        showError("server_problem_try_again");
    }
    $('.loading').hide();
    scrollTop();
}

var resizedImageBase64 = null;

function doSubmit(){
    //console.log(pos.lat, pos.lng, $('#inputComment').val().trim());return;
    if((pos == null || pos.lat == null || pos.lng == null) && $('#inputComment').val().trim() == ""){
        showError('location_or_comment');
        return false;
    }
    console.log("doSubmit()", "Start uploading and sending email");
    $('.loading').show();
    //var file = $('#image')[0].files[0];
    
    var data = new FormData();
	 // Read selected files
	var totalfiles = document.getElementById('image').files.length;
	for (var index = 0; index < totalfiles; index++) {
		console.log("Length: "+index);
		data.append("image[]", document.getElementById('image').files[index]);
	}
    //data.append( 'image',  file), "filename";
    /*if(resizedImageBase64 != null){
        data.append('image_base64', resizedImageBase64);
    }*/
    data.append('lat', pos.lat);
    data.append('lng', pos.lng);
    data.append('comment', $('#inputComment').val());
    data.append('bike_id', $('#inputBikeID').val());
    data.append('email', $('#inputEmail').val());
    data.append('phone_number', $('#inputPhoneNumber').val());
    data.append('google_map_link', googleMapLink);

    //This is for debug purpose only
    var object = {};
        data.forEach(function(value, key){
        object[key] = value;
    });
    console.log('collected data', object);

    //TODO Check if the note is entered or the location is there, require for notes or location
    
    $.ajax({ 
        url: '?_act_=report', 
        type: 'post', 
        data: data, 
        contentType: false, 
        processData: false, 
        success: function(response){ 
            console.log('response', response);
            if(response.success){
                onSuccess();
            } else {
                var message = null;
                if(response.limited){
                    //alert('limit');
                    var limitedIn = response.limited_in;
                    var message = "too_many_sumbmissions";
                }
                //Unknow error
                onError(message);
            }
        },
        error: function(errror){
            onError(error)
        }
    }); 

    
    return false;
}

function scrollTop(){
    $('.box').stop().animate({scrollTop:0}, 200, 'swing', function() {});
}



function imageToDataUri(img, width, height) {

    // create an off-screen canvas
    var canvas = document.createElement('canvas'),
        ctx = canvas.getContext('2d');

    // set its dimension to target size
    canvas.width = width;
    canvas.height = height;

    // draw source image into the off-screen canvas:
    ctx.drawImage(img, 0, 0, width, height);

    // encode image to data-uri with base64 version of compressed image
    return canvas.toDataURL('image/jpeg', 0.9); //quality
    }

//Capture image
function readURL(input) {
    if (input.files) {
	    var files = e.target.files,
        filesLength = files.length;
      for (var i = 0; i < filesLength; i++) {
        var f = files[i]
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {
          var file = e.target;
	  console.log(f.name);
          $("<span class=\"pip\">" + "<img class=\"imageThumb\" src=\"" + e.target.result + "\" width=\"100\" hieght=\"100\" title=\"" + f.name + "\"/>" + "<br/><span class=\"remove\">Remove image</span>" + "</span>").insertAfter("#coverImage");
          $(".remove").click(function(){
            $(this).parent(".pip").remove();
			$("#image").val('');
          });
        });
        fileReader.readAsDataURL(f);
      }
		/*var img;
		var inputfield;
		var filesAmount = input.files.length;
		console.log(filesAmount);
		for (i = 0; i < filesAmount; i++) {
		console.log(i);	
		//console.log('Input value after upload: ', inputfield.value)
		//var imgPath = inputfield.value;
		//img = imgPath;
        var reader = new FileReader();
        console.log('run 1')
        reader.onload = function (event) {
			console.log(event.target);
			var imageBase64 = event.target.result;
			//console.log(imageBase64);
			//$($.parseHTML('<img>')).attr('src', event.target.result).appendTo('div.img-wrap');
			//$('#image-display').attr('src', event.target.result);
			//$('#img-wrap').show();
			
			$('.gallery-image').append('<div class="img-wrap" class="img-wrap mb-4"><span class="close">&times;</span><img id="image-display" class="mb-4 form-finding-img" alt="" width="160" height="160" src="'+event.target.result+'"></div>');
			$(".close").click(function(event){
				event.preventDefault(); 
				console.log("11111");
				console.log(reader.readAsDataURL(input.files[i]));
				$(this).parent(".img-wrap").remove();
				//$('#image').val();
				/*img.val = '';
				input.value = null;
				console.log('Input value after remove: ', inputfield.value)*/
			});
			
            /*var imageBase64 = e.target.result;
            var img = new Image();
            img.onload = function() {
                console.log(this.width, this.height, 'kaka');
                var ew = 800;
                var eh = 800;
                var ratio = this.width/ew;
                if(ratio > 1){
                    var eh = this.height/ratio;
                } else {
                    ew = this.width;
                    eh = this.height;
                }
                var newImg = imageToDataUri(this, ew, eh);
                $('#image-display').attr('src', newImg);
                //$('#image-display').show();
                $('#img-wrap').show();
                //$('#image-display').css('background-image', 'url(' + newImg + ')');
                console.log('run 2')
                resizedImageBase64 = newImg;
            }
            img.src = imageBase64; */                   
        };
		//reader.readAsDataURL(input.files[0]);
       /* reader.readAsDataURL(input.files[i]);
		console.log("For loop end");
		}
    }*/
}


function unloadImage(){
    $('#image-display').removeAttr('src');
    resizedImageBase64 = null;
    //$('#image-display').hide();
    $('#img-wrap').hide();
    $("#image").replaceWith($("#image").val('').clone(true));
}

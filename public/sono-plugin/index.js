const domainHttpLink = "https://iso-sono.universfinancepartners.com";
// const domainHttpLink  =  "http://127.0.0.1:8000";
const isoSono = (idOrganisation, referenceOffre, btn) => {


    var s = document.createElement('script');
    s.type = "text/javascript";
    // s.setAttribute('src', domainHttpLink + '/sono-plugin/js/main.js');
    s.setAttribute('src', domainHttpLink + '/sono-plugin/js/main.js');
    // s.onreadystatechange = chargeCss({idOrganisation, referenceOffre})
    s.onload = function () {
        chargeCss({ idOrganisation, referenceOffre, btn })
    }
    // s.onload = test
    document.body.append(s);
}
const chargeCss = (params) => {
    console.log('test', params);
    var s = document.createElement('link');
    s.rel = "stylesheet";
    s.type = "text/css"
    s.setAttribute('href', domainHttpLink + '/sono-plugin/scss/main.css');
    // s.onreadystatechange = getAxios(params)
    s.onload = function () {
        getAxios(params)
    }
    // s.onload = test
    document.head.append(s);
}

const getAxios = (params) => {
    var s = document.createElement('script');
    s.src = 'https://unpkg.com/axios/dist/axios.min.js';
    s.onload = function () {
        console.log(params);

        axios.request({
            method: 'POST',
            url: `${domainHttpLink}/api/get/sono`, data: {
                organisation_reference: params.idOrganisation,
                annonce_reference: params.referenceOffre,
            }
        })
            .then((response) => {
                console.log('response', response.data);

                let embeded = document.getElementById('btn-iso-sono')
                let button = ''
                if (response.data.content && response.data.content.url_music) {
                    if (params.btn == 'modal') {
                        button = chargeBoostraplink({
                            ...params,
                            url: response.data.content.url_music
                        }, function (result) {
                            console.log({ result });

                            embeded.innerHTML = result;
                            new GreenAudioPlayer('.ready-player-1', {
                                showTooltips: true,
                                showDownloadButton: false,
                                enableKeystrokes: true
                            });


                            let timeStart = 0;
                            var myAudio = document.getElementById("myAudio");
                            myAudio.onplay = function () {
                                timeStart = myAudio.currentTime
                            };
    
                            myAudio.onpause = function () {
    
                                axios.request({
                                    method: 'POST',
                                    url: `${domainHttpLink}/api/update/sono/stats`, data: {
                                        annonce_reference: params.referenceOffre,
                                        temps_lecture: myAudio.currentTime - timeStart,
                                    }
                                }).then((responseP) => {
                                    console.log(responseP);
                                }).catch((errorP) => {
                                    console.log(errorP);
                                });
                                timeStart = 0;
                            };
                            myAudio.onended = function () {
                                axios.request({
                                    method: 'POST',
                                    url: `${domainHttpLink}/api/update/sono/stats`, data: {
                                        annonce_reference: params.referenceOffre,
                                        temps_lecture: myAudio.duration - timeStart,
                                    }
                                }).then((responseP) => {
                                    console.log(responseP);
                                }).catch((errorP) => {
                                    console.log(errorP);
                                });
                                timeStart = 0;
                            };

                        })
                    } else {
                        button = injecteButtonAudio(response.data.content.url_music)
                        embeded.innerHTML = button
                        new GreenAudioPlayer('.ready-player-1', {
                            showTooltips: true,
                            showDownloadButton: false,
                            enableKeystrokes: true
                        });
                        
                    let timeStart = 0;
                    var myAudio = document.getElementById("myAudio");
                    myAudio.onplay = function () {
                        timeStart = myAudio.currentTime
                    };

                    myAudio.onpause = function () {

                        axios.request({
                            method: 'POST',
                            url: `${domainHttpLink}/api/update/sono/stats`, data: {
                                annonce_reference: params.referenceOffre,
                                temps_lecture: myAudio.currentTime - timeStart,
                            }
                        }).then((responseP) => {
                            console.log(responseP);
                        }).catch((errorP) => {
                            console.log(errorP);
                        });
                        timeStart = 0;
                    };
                    myAudio.onended = function () {
                        axios.request({
                            method: 'POST',
                            url: `${domainHttpLink}/api/update/sono/stats`, data: {
                                annonce_reference: params.referenceOffre,
                                temps_lecture: myAudio.duration - timeStart,
                            }
                        }).then((responseP) => {
                            console.log(responseP);
                        }).catch((errorP) => {
                            console.log(errorP);
                        });
                        timeStart = 0;
                    };
                    }

                }


            });
    };
    document.body.append(s);

}

const chargeBoostraplink = (params, callback) => {
    var s = document.createElement('link');
    s.rel = "stylesheet";
    s.setAttribute('href', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css');
    // s.onreadystatechange = getAxios(params)
    s.onload = function () {
        return chargeJquery(params, function (result) {
            callback(result);
        })
    }
    // s.onload = test
    document.head.append(s);
}

const chargeBoostrapScript = (params, callback) => {
    var s = document.createElement('script');
    s.setAttribute('src', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js');
    // s.onreadystatechange = chargeCss({idOrganisation, referenceOffre})
    s.onload = function () {
        callback(injecteButtonModale(params))
    }
    // s.onload = test
    document.body.append(s);
}

const chargeJquery = (params, callback) => {
    var s = document.createElement('script');
    s.setAttribute('src', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
    // s.onreadystatechange = chargeCss({idOrganisation, referenceOffre})
    s.onload = function () {
        return chargeBoostrapScript(params, function (result) {
            callback(result);
        })
    }
    // s.onload = test
    document.body.append(s);
}

const injecteButtonModale = (params) => {
    return `<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Jouer audio</button>

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
      <div class="modal-dialog">
      
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Modal Header</h4>
          </div>
          <div class="modal-body">
            ${injecteButtonAudio(params.url)}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
        
      </div>
    </div>`
}
const injecteButtonAudio = (url) => {
    return `<div class="ready-player-1">
    <audio id="myAudio">
        <source src="${url}" type="audio/mp3">
    </audio>
</div>`
}
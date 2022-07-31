if (typeof RMap === 'undefined') {
  window.RMap = {
    defaults: {
      wait: {
        timeout: 10,
        limit: 100
      }
    },
    providers: {},
    result: null,
    initMap: function (dom) {
      if ($(dom).length == 0) {
        console.error('DOM was not found')
        return false
      }
      var res = {
        'prov': 'google'
      }
      res.dom = $(dom).first()[0]
      this.blockMap(res)
      switch (this.providers[res.prov]) {
        case 'OK':
          console.log('prov ok')
          this.createMap(res)
          break
        case 'BUSY':
        console.log('prov busy')
          this.loadMap(res)
          break
        default:
          console.log('prov unknown')
          this.providers[res.prov] = 'BUSY'
          this.loadProvider(res)
          this.result = res
      }
      return res
    },
    setBounds: function (res, bounds) {
      var that = this
      this.runMapFunction(res, function () { that.setGoogleBounds(res, bounds) })
    },
    setPlaces: function (res, places) {
      var that = this
      this.runMapFunction(res, function () { that.setGooglePlaces(res, places) })
    },
    loadMap: function (res) {
      var that = this
      this.runFunction(res, function () { that.createMap(res) })
    },
    blockMap: function (res) {
      console.log('block map', res.dom)
      if (res.status !== 'blocked') {
        res.status = 'blocked'
        if (res.callback && typeof res.callback.blockMap === 'function') {
          res.callback.blockMap()
        } else {
          $(res.dom).css('opacity', 0.2)
        }
      }
    },
    unblockMap: function (res) {
      console.log('unblock map', res.dom)
      if (res.status === 'blocked') {
        res.status = 'running'
        if (res.callback && typeof res.callback.unblockMap === 'function') {
          res.callback.unblockMap()
        } else {
          $(res.dom).css('opacity', 1)
        }
      }
    },
    errorMap: function (res, msg) {
      console.log('error map', res.dom)
      if (res.callback && typeof res.callback.errorMap === 'function') {
        res.callback.errorMap(msg)
      } else {
        console.error('map error on: ', res, (typeof msg !== 'undefined' ? msg : ''))
      }
    },
    loadProvider: function(res) {
      switch (res.prov) {
        case 'google':
          console.log('loading google')
          this.loadGoogleMaps(res)
          break;
        default:
        console.log('loading yandex?')
      }
    },
    checkProvider: function(prov) {
      var status = 'unknown'
      if (this.providers[prov]) {
        status = this.providers[prov]
      }
      return status
    },
    createMap: function (res) {
      switch (res.prov) {
        case 'google':
          console.log('loading google')
          this.createGoogleMap(res)
          break;
        default:
        console.log('loading yandex?')
      }
    },
    createGoogleMap: function (res) {
      var that = this
      res.map = new google.maps.Map(res.dom, {
        // center: { lat: -34.397, lng: 150.644 },
        // zoom: 8
      })
      res.map.addListener('idle', function() {
        console.log('map is idle now')
        if (res.callback && typeof res.callback.idle === 'function') { res.callback.idle() }
        if (res.status === 'blocked') {
          that.unblockMap(res)
        }
      })
      res.infowindow = new google.maps.InfoWindow()

    },
    loadGoogleMaps: function (res) {
      if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
        this.providers[res.prov] = 'OK'
        this.createMap(res)
        return
      }
      var that = this
      if (typeof window.RMapGoogleInitMap !== 'function') {
        window.RMapGoogleInitMap = function () {
          that.CallbackMap()
        }
      }
      var s = $('<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCmczX1SXXu1Vefc1odTTm4ORihX36BraU&callback=RMapGoogleInitMap"></script>')
      // var c = $('<script async defer src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>')
      var c = $('<script async defer src="catalog/view/javascript/markerclusterer.min.js"></script>')
      $('head').append(c).append(s)
    },
    setGoogleBounds: function (res, bounds) {
      res.map.fitBounds(
        new google.maps.LatLngBounds(
          new google.maps.LatLng(bounds[0][0], bounds[0][1]),
          new google.maps.LatLng(bounds[1][0], bounds[1][1])
        )
      )
    },
    setGooglePlaces: function (res, places) {
      var markers = []
      var that = this
      places.forEach(function(place) {
        var mop = {
          map: res.map,
          position: new google.maps.LatLng(place['GPS'][0], place['GPS'][1]),
        }
/*
        if (place.Provider === 'cdek') {
          mop.label = 'C';
        } else if (place.Provider === 'boxberry') {
          mop.label = 'B';
        }
*/
        if (place.address) { mop['title'] = place.address }
        var marker = new google.maps.Marker(mop)
        if (places.length > 10) { markers.push(marker) }
        marker.addListener('click', function (l) {
          res.map.setCenter(l.latLng)
          res.map.setZoom(17)
          res.infowindow.setPosition(l.latLng)
          if (res.callback && typeof res.callback.infowindow_content === 'function') {
            res.infowindow.setContent(res.callback.infowindow_content(place))
          } else {
            res.infowindow.setContent(place.address)
          }
          res.infowindow.open(res.map)
        })
        place.marker = {
          click: function () {
            google.maps.event.trigger(marker, 'click', { latLng: mop.position })
          }
        }
        //
        //
      })
      if (markers.length > 10) {
        this.wait(
          function () { return typeof MarkerClusterer === 'function' },
          function () {
            res.markerCluster = new MarkerClusterer (
              res.map, markers,
              // { imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m' }
              { imagePath: 'image/icon/m' }
            )
          }
        )
      }
    },
    CallbackMap: function (res) {
      if (typeof res === 'undefined') {
        res = this.result
        this.result = null
        this.providers[res.prov] = 'OK'
      }
      this.createMap(res)
    },
    runMapFunction: function (res, func) {
      var that = this
      this.wait(
        function () { return res.map },
        func,
        function () { that.errorMap(res) },
        100,
        50
      )
    },
    runFunction: function (res, func) {
      var that = this
      this.wait(
        function () { return that.checkProvider(res.prov) === 'OK' },
        func,
        function () { that.errorMap(res) },
        100,
        50
      )
    },
    wait: function (expr, success, error, timeout, limit, step) {
      // console.log('wait', expr, typeof expr)
      var that = this
      if (typeof expr === 'function') {
        // console.log('f expr=', expr())
        if (expr()) {
          if (typeof success === 'function') { success() }
          return
        }
      } else if (typeof expr === 'string') {
        // console.log('e expr=', eval(expr))
        try {
          if (eval(expr)) {
            if (typeof success === 'function') { success() }
            return
          }
        } catch (e) {
          console.error(e)
          return
        }
      } else if (expr) {
        // console.log('c expr=', expr)
        if (typeof success === 'function') { success() }
        return
      }
      if (step) {
        if (limit <= 0) {
          if (typeof error === 'function') {
            error()
          }
          return
        }
        setTimeout(function () { that.wait(expr, success, error, timeout, limit - 1, true) }, timeout)
      } else {
        if (typeof timeout === 'undefined') {
          timeout = this.defaults.wait.timeout
        }
        if (typeof limit === 'undefined') {
          limit = this.defaults.wait.limit
        }
        setTimeout(function () { that.wait(expr, success, error, timeout, limit, true) }, timeout)
      }
    }
  }
}
if (typeof window.RMapInit === 'function') {
  RMapInit()
}

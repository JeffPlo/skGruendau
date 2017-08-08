###*
  * Your JS/CoffeeScript goes here
  * Components like custom classes are in components/
  * Third party libraries are in vendor/ or /bower_components/
  *
  * For CoffeeScript style guide please refer to
  * https://github.com/MBV-Media/CoffeeScript-Style-Guide
  *
  * @since 1.0.0
  * @author R4c00n <marcel.kempf93@gmail.com>
  * @license MIT
###
'use strict'

jQuery ($) ->
  ###*
    * Initialize modules/plugins/etc.
    *
    * @since 1.0.0
    * @return {void}
  ###
  init = ->
    $(document).foundation()
    initEvents()

  ###*
    * Initialize global events.
    *
    * @since 1.0.0
    * @return {void}
  ###
  initEvents = ->
    $(window).load onReady
    $(document).on 'click', '.gp-tabs .not-loaded', getPostContent
    $(document).on 'click', '.table-tabs .not-loaded', getTableContent


  ###*
    * Log "Everything loaded".
    *
    * @since 1.0.0
    * @return {void}
  ###
  onReady = ->
    $('.gp-tabs').tabs()
    $('.table-tabs').tabs()

  getTableContent = ->
    element = $(this)
    if(element.hasClass('not-loaded'))
      leagueId = element.data('league-id')
      teamId = element.data('team-id')
      type = element.data('type')

      targetElement = $('#tabs-' + teamId)
      data = {
        action: 'get_data'
        leagueId: leagueId
        type: type
        function: 'getTableContent'
        nonce: adminAjax.nonce
      }
      $.ajax
        url: adminAjax.ajaxurl
        type: 'post'
        dataType: 'html'
        data: data
        error: (jqXHR, textStatus, errorThrown) ->
          console.log(textStatus, errorThrown)
        success: (response) ->
          targetElement.html(response)
          element.removeClass('not-loaded')

  getPostContent = ->
    element = $(this)
    if(element.hasClass('not-loaded'))
      postId = element.data('post-id')

      targetElement = $('#tabs-' + postId)
      data = {
        action: 'get_data'
        postId: postId
        function: 'getPostContent'
        nonce: adminAjax.nonce
      }
      $.ajax
        url: adminAjax.ajaxurl
        type: 'post'
        dataType: 'html'
        data: data
        error: (jqXHR, textStatus, errorThrown) ->
          console.log(textStatus, errorThrown)
        success: (response) ->
          targetElement.html(response)
          element.removeClass('not-loaded')


  init()
//Execute Google Analytics and Facebook Pixel events
function googleFacebookEvents(eventCategory, eventName,  position){
    //Google Analytics
    gtag('event', eventName, {'event_category': eventCategory, 'event_label': position});
    //Facebook Pixel
    // fbq('trackCustom', eventCategory, {eventNameKey: eventName});
}

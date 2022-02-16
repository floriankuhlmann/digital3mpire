
document.addEventListener("DOMContentLoaded", function() {

    // add blormwidgets to the wordpress-widget-box
    let blormDisplayPostsWidgets = document.getElementsByClassName("blorm-display-posts-widget");
    Array.from(blormDisplayPostsWidgets).forEach(function(blormDisplayPostsWidget){
        console.log("blorm | web-app init: wp-widget");

        let allBlormDisplayPostsWidgetElements = blormDisplayPostsWidget.getElementsByClassName("blorm-display-posts-widget-element");
        Array.from(allBlormDisplayPostsWidgetElements).forEach(function(BlormWidgetElement){

            // the container holds the data
            let id = BlormWidgetElement.dataset.postid;
            postData = blormapp.getPostById(id);

            if (Object.keys(postData).length !== 0) {

                // this is the menue bar inside the image container
                blormWidgetMenuBar = blormWidgetBuilder.GetBlormWidgetContainer(postData);
                if( BlormWidgetElement.getElementsByTagName('img').length > 0) {
                    // there is an image
                    // img element that will be wrapped
                    var imgEl = BlormWidgetElement.getElementsByTagName('img')[0];
                    //blormMenuBar.AddMenueToImage(imgEl);
                    blormWidgetBuilder.AddMenueToImage(imgEl, blormWidgetMenuBar);
                    console.log("blorm | initialized post id:" + postData.PostId + " for website: " + postData.OriginWebsiteName);
                }
            }
        });
    });
});



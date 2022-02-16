/*
**  Blorm Init Widget on image
*/

document.addEventListener("DOMContentLoaded", function() {

    console.log("blorm | web-app init: on image");

    let BlormPosts = blormapp.getAllBlormPosts();
    Array.from(BlormPosts).forEach(function(BlormPost){

        // the 'blorm-post-data'-container holds the relevant postdata we need to connect with the remote data
        let BlormWidget = BlormPost.getElementsByClassName("blormWidget")[0];

        if (typeof BlormWidget !== "undefined") {
            let postId = BlormWidget.dataset.postid;
            let postData = blormapp.getPostById(postId);

            // integrate the widget in the posts. first way put the widget on the image
            if (Object.keys(postData).length !== 0) {
                // this is the menue bar inside the image container
                blormWidgetMenuBar = blormWidgetBuilder.GetBlormWidgetContainer(postData);

                if (blormapp.postConfig.specialCssClassForPostImg !== "") {
                    console.log(blormapp.postConfig.specialCssClassForPostImg );
                    var imgEl = BlormPost.getElementsByClassName(blormapp.postConfig.specialCssClassForPostImg)[0];
                    console.log(imgEl);
                    if (typeof imgEl !== "undefined") {
                        //blormMenuBar.AddMenueToImage(imgEl);
                        blormWidgetBuilder.AddMenueToImage(imgEl, blormWidgetMenuBar);
                    }
                } else {
                    if( BlormPost.getElementsByTagName('img').length > 0) {
                        // there is an image
                        // img element that will be wrapped
                        var imgEl = BlormPost.getElementsByTagName('img')[0];
                        console.log(imgEl);
                        //blormMenuBar.AddMenueToImage(imgEl);
                        blormWidgetBuilder.AddMenueToImage(imgEl, blormWidgetMenuBar);
                        //return;
                    }
                }
            }
            console.log("blorm | initialized post id:" + postData.PostId + " for website: "+postData.OriginWebsiteName);
        }
    });

});



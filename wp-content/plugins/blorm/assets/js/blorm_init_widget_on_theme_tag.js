/*
**  Blorm Init Widget in theme tag
 */

document.addEventListener("DOMContentLoaded", function() {

    console.log("web-app init: mode template tag");
    // add blormwidgets to the wordpress-widget-box
    let blormTemplateTagWidgets = document.getElementsByClassName("blormwidget-template-tag");

    Array.from(blormTemplateTagWidgets).forEach(function(blormTemplateTagWidget){

        if (blormapp.postConfig.float === "float_right" || blormapp.postConfig.float === "") {
            blormTemplateTagWidget.style.float = "right";
        }

        if (blormapp.postConfig.float === "float_left") {
            blormTemplateTagWidget.style.float = "left";
        }

        // the container holds the data
        let id = blormTemplateTagWidget.dataset.postid;
        let postData = blormapp.getPostById(id);

        if (Object.keys(postData).length !== 0) {
            //blormMenuBar = new blorm_menue_bar(postData);
            blormTemplateTagWidget.appendChild(blormWidgetBuilder.GetBlormWidgetContainerMenu(postData));
        }
        console.log("blorm | initialized post id:" + postData.PostId + " for website: "+postData.OriginWebsiteName);
    });
});



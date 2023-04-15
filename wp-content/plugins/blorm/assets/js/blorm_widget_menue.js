class blorm_menue_bar {

    constructor(post) {

        this.md = new MobileDetect(window.navigator.userAgent);

        /* setup config */
        this.blormPostData = post;

        // path to the plugin assets
        this.blormAssets = blormapp.postConfig.blormAssets;

        // config option, float the widget to left or right
        this.widgetFloat = blormapp.postConfig.float;

        // a class to wrap the widget for better css styling
        this.classForWidgetPlacement = blormapp.postConfig.classForWidgetPlacement;

        // set the unit for possible position adjustment
        this.positionUnit = "px";
        if (blormapp.postConfig.positionUnit === "unit_px") {
            this.positionUnit = "px";
        }
        if (blormapp.postConfig.positionUnit === "unit_percent") {
            this.positionUnit = "%";
        }

        // blormapp.postConfig.positionTop
        if (blormapp.postConfig.positionTop !== "") {
            this.positionTop = blormapp.postConfig.positionTop;
        }

        this.positionRight = 0;
        if (blormapp.postConfig.positionRight !== "") {
            this.positionRight = blormapp.postConfig.positionRight;
        }

        this.positionBottom = 0;
        if (blormapp.postConfig.positionBottom !== "") {
            this.positionBottom = blormapp.postConfig.positionBottom;
        }

        this.positionLeft = 0;
        if (blormapp.postConfig.positionLeft !== "") {
            this.positionLeft = blormapp.postConfig.positionLeft;
        }

        // config for origin link on widget
        this.OriginWebsiteName = this.blormPostData.OriginWebsiteName;
        this.OriginWebsiteUrl = this.blormPostData.OriginWebsiteUrl;

        // get the activity_id for the post
        this.postId = this.blormPostData.PostId;

        // init the post data
        //this.initBlormPostData();
        this.blormPost = this.blormPostData;

        this.postType = this.blormPostData.PostType;

        // prepare social data
        this.setSocialDataCounters();

        // init status of the social data display
        this.SocialStatus = "invisible";

        // now render the widget
        this.RenderContainerMenu();

        // init the mouseover events for the template
        this.InitHandler();

    }

    setSocialDataCounters() {

        this.ReblogedCount = 0;
        if (typeof(this.blormPost.ReblogedCount) != "undefined") {
            this.ReblogedCount = this.blormPost.ReblogedCount;
        }

        this.SharedCount = 0;
        if (typeof(this.blormPost.SharedCount) != "undefined") {
            this.SharedCount = this.blormPost.SharedCount;
        }

        this.CommentsCount = 0;
        if (typeof(this.blormPost.CommentsCount) != "undefined") {
            this.CommentsCount = this.blormPost.CommentsCount;
        }
    }

    setPowerBarContent(ContentType) {

        this.SocialStatus = ContentType;

        let socialData = new Array();
        let ul = document.createElement("ul");
        let li = document.createElement("li");

        switch(ContentType) {
            case "rebloged":
                socialData = this.blormPost.Rebloged;
                li.innerHTML = "This post was not rebloged anywhere. Perhaps you should start spreading it?";
                break;
            case "shared":
                socialData = this.blormPost.Shared;
                li.innerHTML = "Nobody shared it. Its up to you now.";
                break;
            case "comments":
                socialData = this.blormPost.Comments;
                li.innerHTML = "No comments on this post.";
                break;
            case "info":
                li.classList.add("PowerbarContentText");
                li.innerHTML = "Blorm helps connecting publishers to promote each other and the content they love. Learn more about blorm at <a href=\"http://blorm.io\">blorm.io</a>\n";
                break;
        }

        // if there is interacton data build the list of links
        if (typeof(socialData) != "undefined" && socialData.length > 0) {

            this.listcontent = new Array();
            socialData.forEach(function (item, index, arr) {
                if (item.kind === "reblog") {
                    this.listcontent[index] = {name:item.data.publisher.Name, link:item.data.publisher.Url };
                }
                if (item.kind === "share") {
                    this.listcontent[index] = {name:item.data.publisher.Name, link:item.data.publisher.Url };
                }
                if (item.kind === "comment") {
                    this.listcontent[index] = {name:item.user.data.data.blormhandle, link: "https://blorm.io/profile/"+item.user.data.data.blormhandle };
                }
            }, this);
            let li = document.createElement("li");
            li.innerHTML = "Post is " + ContentType +" on:";
            ul.appendChild(li);
            for (let content of this.listcontent) {
                let li = document.createElement("li");
                let a = document.createElement('a');
                a.href = content.link;
                a.innerHTML = content.name;
                li.appendChild(a);
                ul.appendChild(li);
            }
        } else {
            ul.appendChild(li);
        }

        let c = this.PowerbarContent.firstChild;
        if ( c != null) {
            this.PowerbarContent.removeChild(c);
        }

        this.PowerbarContent.appendChild(ul);
    }

    setPowerBarPosition() {

        this.Powerbar.style.display = "inline";
        let h = this.PowerbarContent.scrollHeight;

        this.Powerbar.style.height = h + 1 + "px";
        this.Powerbar.style.top =  "-" + h + "px";
        this.PowerbarContent.style.backgroundColor = "#000";
        this.PowerbarContent.style.color = "#fff";

    }

    InitHandler() {
        // mouse event for the powerbar when mouse on icons
        this.handlePlusSocialBars = this.blormWidget.getElementsByClassName("blormWidgetPlusSocialBarEventHandler");
        let SocialBar;
        let _this = this;

        this.handleLayerRebloged = this.ContainerDisplay.getElementsByClassName("blormWidgetPlusSocialBarRebloged")[0];
        this.handleLayerShared = this.ContainerDisplay.getElementsByClassName("blormWidgetPlusSocialBarShared")[0];
        this.handleLayerComments = this.ContainerDisplay.getElementsByClassName("blormWidgetPlusSocialBarComments")[0];
        this.handleLayerLogo = this.ContainerDisplay.getElementsByClassName("blormWidgetPlusLogoIcon")[0];

        if (this.md.mobile() == null) {

            for (SocialBar of this.handlePlusSocialBars) {
                SocialBar.addEventListener(
                    "mouseout",
                    function () {
                        _this.Powerbar.style.display = "none";
                    },
                    false
                );
            }

            // keep the powerbar visible as long we use it
            this.Powerbar.addEventListener(
                "mouseover",
                function () {
                    _this.Powerbar.style.display = "inline";
                    _this.setPowerBarPosition();
                },
                false
            );

            this.Powerbar.addEventListener(
                "mouseout",
                function () {
                    _this.Powerbar.style.display = "none";
                },
                false
            );

            this.handleLayerRebloged.addEventListener(
                "mouseover",
                function () {
                    _this.setPowerBarContent("rebloged");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.handleLayerShared.addEventListener(
                "mouseover",
                function () {
                    _this.setPowerBarContent("shared");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.handleLayerComments.addEventListener(
                "mouseover",
                function () {
                    _this.setPowerBarContent("comments");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.handleLayerLogo.addEventListener(
                "mouseover",
                function () {
                    _this.setPowerBarContent("info");
                    _this.setPowerBarPosition();
                    _this.BlormWidgetPlusLogoIconImg.src = _this.blormAssets+"images/blorm_icon_black_3.png";
                },
                true
            );

            this.handleLayerLogo.addEventListener(
                "mouseout",
                function () {
                    _this.BlormWidgetPlusLogoIconImg.src = _this.blormAssets+"images/blorm_icon_black_1.png";
                },
                true
            );

            console.log("blorm menu "+this.postId+" | init handler finished");
        }

        if (this.md.mobile() !== null) {
            this.handleLayerLogo.addEventListener(
                "click",
                function () {
                    if ( _this.BlormWidgetPlusSocial.getAttribute("style") === "display:none" ) {
                        _this.BlormWidgetPlusSocial.setAttribute("style","display:inline");
                        //_this.BlormWidgetPowerText.setAttribute("style","display:inline");
                        _this.BlormWidgetPlusBlormInfo.setAttribute("style","display:inline");
                        _this.BlormWidgetPlusLogoIconImg.src = _this.blormAssets+"/images/blorm_icon_black_3.png";
                        if (this.postType === "blormreblog") {
                            _this.BlormWidgetPlus.classList.add("BorderBottom");
                        }
                    } else {
                        _this.BlormWidgetPlusSocial.setAttribute("style","display:none");
                        //_this.BlormWidgetPowerText.setAttribute("style","display:none");
                        _this.BlormWidgetPlusBlormInfo.setAttribute("style","display:none");
                        _this.BlormWidgetPlusLogoIconImg.src = _this.blormAssets+"/images/blorm_icon_black_1.png";

                        if (this.postType === "blormreblog") {
                            _this.BlormWidgetPlus.classList.remove("BorderBottom");
                        }

                        if ( _this.SocialStatus !== "invisible") {
                            _this.Powerbar.style.display = "none";
                        }
                    }
                },
                true
            );

            this.handleLayerRebloged.addEventListener(
                "click",
                function () {

                    if ( _this.SocialStatus === "rebloged") {
                        _this.Powerbar.style.display = "none";
                        _this.SocialStatus = "invisible";
                        return;
                    }
                    _this.setPowerBarContent("rebloged");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.handleLayerShared.addEventListener(
                "click",
                function () {
                    if ( _this.SocialStatus === "shared") {
                        _this.Powerbar.style.display = "none";
                        _this.SocialStatus = "invisible";
                        return;
                    }
                    _this.setPowerBarContent("shared");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.handleLayerComments.addEventListener(
                "click",
                function () {
                    if ( _this.SocialStatus === "comments") {
                        _this.Powerbar.style.display = "none";
                        _this.SocialStatus = "invisible";
                        return;
                    }
                    _this.setPowerBarContent("comments");
                    _this.setPowerBarPosition();
                },
                true
            );

            this.BlormWidgetPlusBlormInfo.addEventListener(
                "click",
                function () {
                    if ( _this.SocialStatus === "info") {
                        _this.Powerbar.style.display = "none";
                        _this.SocialStatus = "invisible";
                        return;
                    }
                    _this.setPowerBarContent("info");
                    _this.setPowerBarPosition();

                },
                true
            );

        }
    }

    RenderContainerMenu() {

        // create the html widget
        this.blormWidget = document.createElement("div");
        this.blormWidget.className = "blormWidget";

        // the menue is very all the magic comes together
        this.ContainerMenu = document.createElement("div");
        this.ContainerMenu.className = "blormWidgetContainerMenu";

        /*
        the ContainerMenu depends on two elements:
        1. the powerbar is capable for showing the social interactions:
        blogs reblogged the posts, userhandle commenting it, or users who shared the post on blorm
        the powerbar only appears on mouseover

        2. the display part:
        this is the user-menue showing the origin source of the post, presenting the numbers and the icons
        the powerbar ist visible for users
         */

        /* powerbar plus content */
        // we need a container for the content(list of sharing publishers and comments)
        this.PowerbarContent = document.createElement("div");
        this.PowerbarContent.classList.add("blormWidgetPowerbarContent");

        // we need a powerbar and append the content to
        this.Powerbar = document.createElement("div");
        this.Powerbar.classList.add("blormWidgetPowerbar");
        this.Powerbar.appendChild(this.PowerbarContent);

        // add the powerbar to the menue
        this.ContainerMenu.appendChild(this.Powerbar);


        this.ContainerDisplay = document.createElement("div");
        this.ContainerDisplay.classList.add("blormWidgetContainerDisplay");

        // display has two parts the LogoIcon and the SocialIcons list
        this.BlormWidgetPlusLogoIcon = document.createElement("div");
        this.BlormWidgetPlusLogoIcon.classList.add("blormWidgetPlusLogoIcon");
        this.BlormWidgetPlusLogoIcon.classList.add("blormWidgetPlusSocialBarEventHandler");

        this.BlormWidgetPlusLogoIconImg = document.createElement("img");
        this.BlormWidgetPlusLogoIconImg.classList.add("blormWidgetPlusLogoIconImg");
        this.BlormWidgetPlusLogoIconImg.src = this.blormAssets + "/images/blorm_icon_black_1.png";
        //const markupContainerDisplayPlusLogoIcon = ;
        //this.BlormWidgetPlusLogoIcon.innerHTML = `<img src="${this.blormAssets}/images/blorm_icon_black_1.png" class="blormWidgetPlusLogoIconImg">`;
        this.BlormWidgetPlusLogoIcon.append(this.BlormWidgetPlusLogoIconImg);

        // display has two parts the LogoIcon and the SocialIcons list
        this.BlormWidgetPlusBlormInfo = document.createElement("div");
        this.BlormWidgetPlusBlormInfo.classList.add("blormWidgetPlusBlormInfo");
        this.BlormWidgetPlusBlormInfo.classList.add("blormWidgetPlusSocialBarEventHandler");
        let infoUl = document.createElement("ul");
        let infoUlLi = document.createElement("li");
        infoUlLi.innerHTML = `<img src="${this.blormAssets}/icons/circle-info-more-information-detail-glyph.png" class="blormWidgetPlusLogoIconImg">`;
        infoUl.append(infoUlLi);
        this.BlormWidgetPlusBlormInfo.append(infoUl);

        const markupContainerDisplayPlusSocial = `
                                <ul class="blormWidgetPlusSocialBar">
                                    <li class="blormWidgetPlusSocialBarIcon blormWidgetPlusSocialBarEventHandler blormWidgetPlusSocialBarRebloged">
                                        <span class="material-icons">filter_none</span>
                                    </li>
                                    <li class="blormWidgetPlusSocialBarText">
                                        <span class="blormWidgetPlusSocialBarRebloggedCount">${this.ReblogedCount}</span>
                                    </li>
                                    <li class="blormWidgetPlusSocialBarIcon blormWidgetPlusSocialBarEventHandler blormWidgetPlusSocialBarShared">
                                        <span class="material-icons">sync</span>
                                    </li>
                                    <li class="blormWidgetPlusSocialBarText">
                                        <span class="blormWidgetPlusSocialBarSharedCount">${this.SharedCount}</span>
                                    </li>
                                    <li class="blormWidgetPlusSocialBarIcon blormWidgetPlusSocialBarEventHandler blormWidgetPlusSocialBarComments">
                                        <span class="material-icons">chat</span>
                                    </li>
                                    <li class="blormWidgetPlusSocialBarText">
                                        <span class="blormWidgetPlusSocialBarCommentsCount">${this.CommentsCount}</span>
                                    </li>
                                </ul>
                            `;

        this.BlormWidgetPlusSocial = document.createElement("div");
        this.BlormWidgetPlusSocial.classList.add("blormWidgetPlusSocial");
        this.BlormWidgetPlusSocial.innerHTML = markupContainerDisplayPlusSocial;


        this.BlormWidgetPlus = document.createElement("div");
        this.BlormWidgetPlus.classList.add("blormWidgetPlus");

        this.BlormWidgetPlus.appendChild(this.BlormWidgetPlusLogoIcon);

        if (this.md.mobile() !== null) {
            this.BlormWidgetPlus.appendChild(this.BlormWidgetPlusBlormInfo);
        }
        this.BlormWidgetPlus.appendChild(this.BlormWidgetPlusSocial);

        let clearDiv = document.createElement("div");
        clearDiv.setAttribute("style", "clear:both");

        this.BlormWidgetPlus.appendChild(clearDiv);

        this.ContainerDisplay.append(this.BlormWidgetPlus);

        this.BlormWidgetPowerText = document.createElement("div");
        this.BlormWidgetPowerText.classList.add("blormWidgetPowerText");
        if (this.postType === "blormreblog") {
            let originWebsiteLink = document.createElement("a");
            originWebsiteLink.href = this.OriginWebsiteUrl;
            originWebsiteLink.innerHTML = "&#x021B3; "+ this.OriginWebsiteName;
            this.BlormWidgetPowerText.append(originWebsiteLink);
            this.ContainerDisplay.appendChild(this.BlormWidgetPowerText);
            this.BlormWidgetPlus.classList.add("BorderBottom");

        } else {
            this.BlormWidgetPlus.style.border = "0 !important";
        }

        /* put it all together */
        this.ContainerMenu.appendChild(this.ContainerDisplay);

        /* a wraper box to float the menue left or right */
        this.ContainerMenuBox = document.createElement("div");
        if (this.widgetFloat === "float_left") {
            this.ContainerMenuBox.classList.add("FloatLeft");
            this.BlormWidgetPlus.classList.add("FloatLeft");
            this.BlormWidgetPlusLogoIcon.classList.add("FloatLeft");
            this.BlormWidgetPowerText.classList.add("FloatLeft");
            this.BlormWidgetPowerText.classList.add("AlignLeft");
            this.Powerbar.classList.add("PositionLeft");
        }

        if (this.widgetFloat === "float_right") {
            this.ContainerMenuBox.classList.add("FloatRight");
            this.BlormWidgetPlus.classList.add("FloatRight");
            this.BlormWidgetPlusLogoIcon.classList.add("FloatRight");
            this.BlormWidgetPowerText.classList.add("FloatRight");
            this.BlormWidgetPowerText.classList.add("AlignRight");
            this.Powerbar.classList.add("PositionRight");
        }

        //console.log( "md.mobile():" );
        if ( this.md.mobile() !== null) {
            this.BlormWidgetPlusSocial.setAttribute("style","display:none");
            //this.BlormWidgetPowerText.setAttribute("style","display:none");
            this.BlormWidgetPlusBlormInfo.setAttribute("style","display:none");
        } else {
            if (typeof(this.OriginWebsiteName) !== "undefined") {
                //this.ContainerDisplay.remove("blormWidgetContainerDisplay");
            }
        }

        // append the menu to the wrapper
        this.ContainerMenuBox.appendChild(this.ContainerMenu);

        // prepare the widget
        this.blormWidget.appendChild(this.ContainerMenuBox);
    }

    GetWidget() {
        this.setPosition(this.ContainerMenu);
        if (this.classForWidgetPlacement !== "") {
            let blormWidgetClassBox = document.createElement("div");
            blormWidgetClassBox.className = this.classForWidgetPlacement;
            blormWidgetClassBox.append(this.ContainerMenuBox);
            return blormWidgetClassBox;
        }
        return this.blormWidget;
    }

    GetMenue() {
        this.setPosition(this.ContainerMenu);
        if (this.classForWidgetPlacement !== "") {
            let ContainerMenuClassBox = document.createElement("div");
            ContainerMenuClassBox.className = this.classForWidgetPlacement;
            ContainerMenuClassBox.append(this.ContainerMenuBox);
            return ContainerMenuClassBox;
        }
        return this.ContainerMenuBox;
    }

    AddMenueToImage(imgEl) {

        // we want to put the thumbnail link on the image INSIDE our div. so we save it here for later usage (end of function)
        // this is little bit annyoing but needed to mak the blorm widget work for mobile click events
        let imgElOrigLink = imgEl.parentNode;
        let imgElOrigLinkHref = imgElOrigLink.getAttribute('href');

        // new image wrapper div
        let divWrapper = document.createElement('div');
        divWrapper.classList.add("blormWidgetImageWrapper");

        // insert the wrapper before the image + put image in the wrapper
        imgEl.parentNode.insertBefore(divWrapper, imgEl);
        divWrapper.appendChild(imgEl);

        // the div layer for the blormwidget with the menue
        let divLayerWidget = document.createElement('div');
        divLayerWidget.classList.add("blormWidgetImagelayerWidget");
        /* get the menue widget */
        divLayerWidget.append(blormMenuBar.GetWidget());

        let divLayerBlormIconImg = document.createElement('img');
        divLayerBlormIconImg.src = blormapp.postConfig.blormAssets + "/images/blorm_icon_network.png";
        divLayerBlormIconImg.classList.add("blormWidgetImagelayerBlormIconImg");

        // blorm icon on the top corner of the image
        let divLayerBlormIcon = document.createElement('div');
        divLayerBlormIcon.classList.add("blormWidgetImagelayerBlormIcon");
        divLayerBlormIcon.classList.add("topleft");
        divLayerBlormIcon.append(divLayerBlormIconImg);

        // check if there is a link on the image. if not everything ist fine ans easy
        if (imgElOrigLinkHref == null) {
            imgEl.parentNode.insertBefore(divLayerWidget, imgEl.nextSibling);
            imgEl.parentNode.insertBefore(divLayerBlormIcon, imgEl.nextSibling);
            // if there is a link on the image we have to modify a little bit so the link is not laying over the widget
        } else {
            // we rebuild the links on the images and layers to prevent the link from laying over the blorm widget what would cause problems on mobile click events
            let imgLink = document.createElement('a');
            imgElOrigLink.removeAttribute('href');
            imgLink.href = imgElOrigLinkHref;

            // insert the link before the image + image in the link
            imgEl.parentNode.insertBefore(imgLink, imgEl);
            imgLink.appendChild(imgEl);

            imgLink.parentNode.insertBefore(divLayerWidget, imgEl.nextSibling);
            // put a link on the div layer blorm icon
            let divLayerBlormIconLink = document.createElement('a');
            divLayerBlormIconLink.href = imgElOrigLinkHref;
            divLayerBlormIconLink.append(divLayerBlormIcon);

            imgLink.parentNode.insertBefore(divLayerBlormIconLink, imgEl.nextSibling);
            imgLink.appendChild(imgEl);
        }
    }

    setPosition(element) {
        if (this.positionTop !== 0) {
            let x = 0 - this.positionTop;
            element.style.marginTop = x + this.positionUnit;
        }
        if (this.positionRight !== 0) {
            let x = 0 - this.positionRight;
            element.style.marginRight = x + this.positionUnit;
        }
        if (this.positionBottom !== 0) {
            let x = 0 - this.positionBottom;
            element.style.marginBottom = x + this.positionUnit;
        }
        if (this.positionLeft !== 0) {
            let x = 0 - this.positionLeft;
            element.style.marginLeft = x + this.positionUnit;
        }
    }

} // end blorm class

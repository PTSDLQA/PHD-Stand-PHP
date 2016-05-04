<!--//

 /* FlyWeb Components: SMARTSELECT *\

 # This library provides many useful feautures for simple creating and working with COMBO-BOX form field as follows:
 # HTML/CSS styling and/or XSLT design in accordance with your desire, autofill, multiple selection, AJAX-loading, etc.

 # Full documentation, last version download and forum are avaible at http://flyweb.in/fwc.
 # All comments and bugs notifications are welcome.

 # @autor Alexander Zinchuk | Alx | alx.vingrad.ru
 # @version 2.7

 \* Have a nice day ;) Jah love. */



// DOM XML functions
dom = {
load_file : function(xmlfile)
{
  var httpRequest = null;
  if (typeof XMLHttpRequest != 'undefined') {
    httpRequest = new XMLHttpRequest();
  }
  else if(window.ActiveXObject)
  {   httpRequest = Try.these(
        function() { return new ActiveXObject('MSXML2.DomDocument')   },
        function() { return new ActiveXObject('Microsoft.DomDocument')},
        function() { return new ActiveXObject('MSXML.DomDocument')    },
        function() { return new ActiveXObject('MSXML3.DomDocument')   }
      ) || null;
  }
  if (httpRequest != null) {
    httpRequest.open('GET', xmlfile, false);
    httpRequest.send(null);
    return httpRequest.responseXML;
  }
  return null;
},
load_string : function(xmlstr)
{
    if(window.ActiveXObject)
    {   var doc = Try.these(
          function() { return new ActiveXObject('MSXML2.DomDocument')   },
          function() { return new ActiveXObject('Microsoft.DomDocument')},
          function() { return new ActiveXObject('MSXML.DomDocument')    },
          function() { return new ActiveXObject('MSXML3.DomDocument')   }
        ) || false;
        doc.async = false;
        doc.loadXML('<?xml version="1.0" ?>'+xmlstr);
    } else {
        var parser = new DOMParser();
        var doc = parser.parseFromString(xmlstr,"text/xml");
    }
    return doc || null;
},
xslt : function(xmldoc,xsldoc)
{
    if(window.ActiveXObject) {
        return xmldoc.transformNode(xsldoc);
    } else {
        var proc = new XSLTProcessor();
        proc.importStylesheet(xsldoc);
        var xhtmldoc = proc.transformToDocument(xmldoc);
        var ser = new XMLSerializer();
        return ser.serializeToString(xhtmldoc);
    }
},
asXML : function(xmldoc)
{
    if(window.ActiveXObject) {
        return xmldoc.xml;
    } else {
        var ser = new XMLSerializer();
        return ser.serializeToString(xmldoc);
    }
}
}

// JavaScript loader
FWC =
{   fwcp : (!!window.fwcpath) ? window.fwcpath+"/" : '/templates/admin/',   // path from page to FWComponents
    XSLs : [],
    smartselects : [],                                     // array of SmartSelect objects
    initSSarr : [],                                        // array of initial functions
    ns : (window.ActiveXObject || navigator.userAgent.include('Firefox')) ? 'fwc:' : '',
    clipsplitter : parseFloat(navigator.userAgent.substring(navigator.userAgent.indexOf('/')+1,navigator.userAgent.indexOf(' ')+1))>9.5 ? ', ' : ' ',
    offsetZindex : 1000,
    shows : $H(),
    hides : $H(),
    newSmartSelect : function(xmlfile,attr)
    {   attr = attr ? attr.evalJSON() : false;
        xmlfile = xmlfile || 'default';
        var optionsArr = xmlfile.isJSON() ? xmlfile.evalJSON() : false;
        if(optionsArr) xmlfile = 'default';
        var xslfile, xmldoc = (xmlfile !='default') ? dom.load_file(xmlfile) : dom.load_string('<fwc:select id="sselect" xmlns:fwc="http://alx.vingrad.ru/fwc" />');
        var path = xmlfile.substring(0,xmlfile.lastIndexOf('/'));
          path = (path.length>0) ? path+"/" : './';                 // path from .html to .xml, .css, .xsl, etc.
          if(!xmldoc || !xmldoc.firstChild) { alert("FWC:Error -> XML-file '"+xmlfile+"' loading error."); return; }
          var root = xmldoc.getElementsByTagName(this.ns+'select')[0];
          if(!root) { alert("FWC:Error -> fwc:select tag not found."); return; }
          root.setAttribute('path',path);
          for(var i in attr) { root.setAttribute(i,attr[i])};
          var xslfile, _design, _skin = '';
          var _id = root.getAttribute('id');
          if(!_id) { alert("FWC:Error -> fwc:select ID not found."); return; }
          if(root.getAttribute('skin')) _skin = root.getAttribute('skin');
          else
          {   _skin = (window.opera) ? 'ss_opera' : 'ss_winxp';
              root.setAttribute('skin',_skin);
          }
          if(root.getAttribute('design'))
          {   _design = path+root.getAttribute('design');
              xslfile = _design+".xsl";
          }
          else
          {   _design = this.fwcp+"fwc/design/design.xsl";
              xslfile = _design;
          }
          for(var i=0;i<optionsArr.length;i++)
          xmldoc.getElementsByTagName(this.ns+'select')[0].appendChild(this.buildOption(optionsArr[i],xmldoc));
        var xsldoc = dom.load_file(xslfile);
          if(!xsldoc.firstChild) { alert("FWC:Error -> XSL-file '"+xslfile+"' loading error."); return;}

        var xhtml = dom.xslt(xmldoc,xsldoc);

        xsldoc = this.compactXSL(xsldoc,_skin,_id); // saves the light XSL document for AJAX/appendOptions XSLT
        this.XSLs[_design] = xsldoc;

          return xhtml;
    },
    buildOption : function(arr,xmldoc)
    {   var o = xmldoc.createElementNS ? xmldoc.createElementNS('http://alx.vingrad.ru/fwc','fwc:option') : xmldoc.createNode('element','fwc:option','http://alx.vingrad.ru/fwc');
        o.appendChild(xmldoc.createTextNode(arr[0]));
        if(arr[1]) o.setAttribute('value',arr[1]);
        if(arr[2])
        {   var attr = arr[2];
            for(var i in attr) o.setAttribute(i,attr[i]);
        }
        return o;
    },
    show : function(obj,step,max)
    {   if(obj.offsetTop < max)
        {   step = (step+obj.offsetTop <= max ? step : max-obj.offsetTop);
            obj.style.top = obj.offsetTop + step + 'px';
            var clip = obj.style.clip.split(FWC.clipsplitter);
            clip[0] = "rect("+(parseInt(clip[0].substring(5,clip[0].length-2))-step)+"px";
            obj.style.clip = clip.join(FWC.clipsplitter);

        }
        else
        {   clearInterval(FWC.shows[obj.id.replace("_options","")]);
            FWC.shows[obj.id.replace("_options","")] = false;
      			if(window.opera)
      				obj.style.overflow = 'auto';
      			else
      				obj.style.overflowY = 'auto';
        }
    },
    hide : function(obj,step,max,s,callback)
    {   if(obj.offsetTop > max)
        {   step = (obj.offsetTop-step > max ? step : obj.offsetTop-max+1);
            obj.style.top = obj.offsetTop - step + 'px';
                        var clip = obj.style.clip.split(", ");
            clip[0] = "rect("+(parseInt(clip[0].substring(5,clip[0].length-2))+step)+"px";
            obj.style.clip = clip.join(", ");
        }
        else
        {   clearInterval(FWC.hides[obj.id.replace("_options","")]);
            FWC.hides[obj.id.replace("_options","")] = false;
            if(callback) s[callback]();
        }
    },
    // removes the <xsl:template match="fwc:select"> directory from XSL-file for FWC_sSelect.appendOptions() method.
    compactXSL : function(xsldoc,skin,id)
    {   var ns = (window.ActiveXObject || navigator.userAgent.include('Firefox')) ? 'xsl:' : '';
        var a = xsldoc.getElementsByTagName(ns+'template');
        var n = 0;
        a[n].parentNode.removeChild(a[0]);
        if(window.ActiveXObject) n++;
        var title_params = a[n].getElementsByTagName(ns+'param');
          title_params[0].appendChild(xsldoc.createTextNode(skin));
          title_params[1].appendChild(xsldoc.createTextNode(id));
        var options_params = a[n+1].getElementsByTagName(ns+'param');
          options_params[0].appendChild(xsldoc.createTextNode(skin));
          options_params[1].appendChild(xsldoc.createTextNode(id));

        return xsldoc;
    },
    // the same result comes from server
    loadCompactedXSL : function(obj,callback)
    {   var design = location.href.substring(0,location.href.lastIndexOf('/')+1)+(obj.design.startsWith("./")?obj.design:obj.design.replace('.xsl',''));
        new Ajax.Request(FWC.fwcp+'fwc/php/ajaxload.php',{
            parameters : {design:design,skin:obj.skin,id:obj.id},
            onComplete : function(req)
            {   FWC.XSLs[obj.design] = req.responseXML || 'no';
                callback();
            }
        });
    },
    XSLs_addAttrs : function(xsldoc,obj)
    {   var xsldoc = this.XSLs[obj.design];
        var ns = (window.ActiveXObject || navigator.userAgent.include('Firefox')) ? 'xsl:' : '';
        var a = xsldoc.getElementsByTagName(ns+'template');
        var title_params = a[0].getElementsByTagName(ns+'param');
          title_params[0].firstChild.nodeValue = obj.skin;
          title_params[1].firstChild.nodeValue = obj.id;
        var options_params = a[1].getElementsByTagName(ns+'param');
          options_params[0].firstChild.nodeValue = obj.skin;
          options_params[1].firstChild.nodeValue = obj.id;

        return xsldoc;
    },
    evalSmartSelectTags : function(el)
    {   el = el || document.getElementsByTagName('body')[0];
        var sss =  !navigator.userAgent.include('Safari') ? $(el).getElementsBySelector("div.smartselect") : $$("div.smartselect");
		for(var i=0;i<sss.length;i++)
        { if(sss[i].childNodes.length == 0)
            { sss[i].innerHTML = FWC.newSmartSelect(sss[i].getAttribute('source'),sss[i].getAttribute('attr'));
			  var _id = sss[i].getElementsBySelector("div[class~=fwcss]")[0].getAttribute('id').replace("_select","");
			  eval(sss[i].getElementsBySelector("script."+_id+"_script")[0].innerHTML);
              FWC.initSSarr[_id]();
            }
        }
    }
}

Event.observe(document,'mousedown',function(e)
{   var anc = Event.element(e).ancestors();
    var ss = anc.find(function(s) {return s.className ? s.className.indexOf('fwcss') != -1 : false})
    for(var i=0;i<FWC.smartselects.length;i++)
    {   if((ss ? FWC.smartselects[i].id != ss.id.replace("_select","") : true) && (FWC.smartselects[i].optionsDiv.visible() && !FWC.hides[FWC.smartselects[i].id]))
        FWC.smartselects[i].hideOptions();
    }
});

// ************ FWC:SmartSelect properties and methods definition ************ //

function FWC_sSelect(id,mode,skinpath,design,skin,selectTitle,multiple,readonly,ownvalues,fillstart,ontype,fewprefix,fewall,splitter,maxheight,usectrlkey,onchange,onclose,oninit,showspeed,hidespeed,uneven)
{   var t = this;
    FWC.smartselects.push(this);
    this.newCSS = false;
    if(!$(skin+'_linkcss'))
    {   this.newCSS = true;
        skinpath = "/templates/admin/"+skinpath;
        var css = document.createElement('link');
        css.setAttribute('rel','StyleSheet');
        css.setAttribute('type','text/css');
        css.setAttribute('href',skinpath+'.css');
        css.setAttribute('id',skin+'_linkcss');
        Event.observe(css,'load',function(e){t.initImages();});
        document.getElementsByTagName('HEAD')[0].appendChild(css);
    }

    this.id = id;
    this.mode = mode;
    this.select = $(this.id+"_select");        // main DIV object
    this.optionsDiv = $(this.id+"_options");   // OPTIONS Div object
    this.title = $(this.id+"_title");          // TITLE INPUT object
    this.tbutton = $(this.id+"_tbutton");      // BUTTON INPUT object
    this.informDiv = $(this.id+"_inform");     // hidden layer for input:hidden fields
    this.top = '';                             // -y- of OPTIONS Div
    this.optionsDiv.onselectstart = function(e) { window.event.returnValue = false; return false; }
    this.optionOver = false;                    // mouseover option
    this.singleActive = false;
    this.edited = false;
    this.hid = false;

    if(this.mode != 'text' && skin.startsWith("ss_")) this.title.style.borderRight = '0px';

    // returns OPTION by index
    this.getOptionNode = function(s) { return (typeof(s) == 'number') ? (this.optionsNodes[s]?this.optionsNodes[s]:this.optionsNodes.last()) : (s||this.optionsNodes.last()); }

    this.ontype = function(e)
    {   e = e || window.event;
        if(this.readonly) return false;
        if(e.keyCode == '38' || e.keyCode == '40' || e.keyCode == '37' || e.keyCode == '39' || e.keyCode == '13' || e.keyCode == '36' || e.keyCode == '35') return false;
        this.edited = true;
        if((this.title.value.length < this.fillstart) && (this.mode == 'text' ? true : this.title.value.length > 0)) { this.hideOptions(true); return false; };
        if(!ontype || !!ontype()) this.titleKeyUp(e)
    }

    this.design       = (!!window.fwcpath && design.indexOf("fwc") != -1) ? window.fwcpath+"/"+design : design;
    this.selectTitle  = selectTitle;
    this.multiple     = multiple != '' ? !!eval(multiple) : false;
    this.readonly     = this.mode == 'select' ? true : (readonly != '' ? !!eval(readonly) : false);
    this.ownvalues    = ownvalues != '' ? !!eval(ownvalues) : (this.mode == 'text');
    this.fillstart    = fillstart || 1;
    this.fewprefix    = fewprefix != '' ? fewprefix : false;
    this.fewall       = fewall;
    this.splitter     = splitter || ", ";
    this.skin         = skin;
    this.maxheight    = maxheight;
    this.usectrlkey   = usectrlkey != '' ? !!eval(usectrlkey) : false;
    this.oninit       = oninit || false;
    this.onchange     = onchange || false;
    this.onclose      = onclose || false;
    this.showspeed    = showspeed != '' ? parseInt(showspeed) : 20;
    this.hidespeed    = hidespeed || 0;
    this.uneven       = uneven;

    Event.observe(this.title,'mouseover',function(){this.title.focus()}.bind(this));
    Event.observe(this.tbutton,'mouseover',function(){this.title.focus()}.bind(this));
    Event.observe(this.title,'focus', function(){ if(this.title.form) this.title.form.observe('submit', Event.stop); }.bind(this));
    Event.observe(this.title,'blur',  function(){ if(this.title.form) this.title.form.stopObserving('submit', Event.stop); }.bind(this));

    this.tbutton.tabIndex=-1;

    this.initOptions(true);

}

// defines selected options and fills VALUE property
FWC_sSelect.prototype.initOptions = function(firsttime)
{   this.optionsNodes = [];
    this.value = [];
    if(!this.uneven)
    {   var list = $A(this.optionsDiv.childNodes);
        if(list.length > 0)
          if(list[0].tagName)
            if(list[0].tagName.toLowerCase() == 'fwc:select')
              list = $A(list[0].childNodes);
        var cont = true;
        for(var i=0;i<list.length;i++) {
            var a = list[i];
            if(a.nodeType != 1) continue;
            if(a.getAttribute('val')) {
                this.optionsNodes.push(a);
                if(a.className.indexOf("_Sel") != -1 && cont) {
                    this.value.push(a.getAttribute('val'));
                    if(!this.multiple) {
                        this.singleActive = a;
                        cont = false;
                    }
                }
            }
        }
    }
    else
    {   this.optionsNodes = Element.getElementsBySelector(this.optionsDiv,"span[val]");
        for(var i=0;i<this.optionsNodes.length;i++)
        {   var a = this.optionsNodes[i];
            if(a.className.indexOf("_Sel") != -1)
            {   this.value.push(a.getAttribute('val'));
                if(!this.multiple) this.singleActive = a;
            }
        }
    }

    this.shownOptions = this.optionsNodes.slice(0); // array of real-time visible options (AUTOFILL feauture)
    if(firsttime)
    {   this.select.style.zIndex = window.ActiveXObject ? -Position.cumulativeOffset(this.select)[1]+document.body.offsetHeight+1000 : FWC.offsetZindex--;
        this.select.style.visibility = 'visible';
        var t = this;
        if((!window.ActiveXObject && !window.opera) || !this.newCSS) setTimeout(function() {t.initImages()},200);
    }
    this.setHValue(true);
    if(firsttime && this.oninit) return setTimeout(this.oninit,50);
}

// loads images and sets width of optionsDiv
FWC_sSelect.prototype.initImages = function()
{   this._images = new Array();
    var i_t = this.title.getStyle('backgroundImage').replace(/"/gi,'');
    var i_t = i_t.substring(i_t.indexOf('(')+1,i_t.lastIndexOf(')'));
    this._images[0] = new Array();
    this._images[0][0] = new Image();
    this._images[0][0].src = i_t;

    if(i_t.indexOf('_ou') != -1)
    {   this._images[0][1] = new Image();
        this._images[0][1].src = i_t.replace('_ou','_ov');
        this._images[0][2] = new Image();
        this._images[0][2].src = i_t.replace('_ou','_do');
    }

    if(this.tbutton)
    {   var i_b = this.tbutton.getStyle('backgroundImage').replace(/"/gi,'');
        var i_b = i_b.substring(i_b.indexOf('(')+1,i_b.lastIndexOf(')'));
        this._images[1] = new Array();
        this._images[1][0] = new Image();
        this._images[1][0].src = i_b;

        if(i_b.indexOf('_ou') != -1)
        {   this._images[1][1] = new Image();
            this._images[1][1].src = i_b.replace('_ou','_ov');
            this._images[1][2] = new Image();
            this._images[1][2].src = i_b.replace('_ou','_do');
        }
    }
}

FWC_sSelect.prototype.setDimensions = function()
{   var setwidth = window.ActiveXObject ? this.optionsDiv.currentStyle.width : this.optionsDiv.getStyle('max-width'); // isset style.width && style.max-width
    var isset = setwidth && (setwidth != 'none' && setwidth != '-1px') && setwidth != 'auto';
    setwidth = window.ActiveXObject ? setwidth : Math.round((parseFloat(setwidth) / parseFloat(this.optionsDiv.parentNode.offsetWidth) * 100)) + "%";
    this.optionsDiv.style.width = !isset ? this.title.parentNode.offsetWidth+'px' : (setwidth.indexOf("%") != -1 ? parseInt(this.title.parentNode.offsetWidth*setwidth.replace("%","")/100)+'px' : setwidth);
    this.optionsDiv.style.height = this.maxheight ? this.maxheight+'px' : '100px';
    this.top = this.top || this.optionsDiv.offsetTop;
}

// handlers for open-close button events
FWC_sSelect.prototype.titleButtonRollOver = function()
{   if(this._images[1][1]) this.tbutton.style.backgroundImage = "url('"+this._images[1][1].src+"')";
    if(this._images[0][1]) this.title.style.backgroundImage = "url('"+this._images[0][1].src+"')";
}

FWC_sSelect.prototype.titleButtonRollOut = function()
{   if(this._images[1][1]) this.tbutton.style.backgroundImage = "url('"+this._images[1][0].src+"')";
    if(this._images[0][1]) this.title.style.backgroundImage = "url('"+this._images[0][0].src+"')";
}

FWC_sSelect.prototype.titleRollUpDown = function(way)
{   if(way == 'down')
    {   if(this._images[1][2]) this.tbutton.style.backgroundImage = "url('"+this._images[1][2].src+"')";
        if(this._images[0][2]) this.title.style.backgroundImage = "url('"+this._images[0][2].src+"')";
    }
    else
    {   if(this._images[1][1]) this.tbutton.style.backgroundImage = "url('"+this._images[1][1].src+"')";
        if(this._images[0][1]) this.title.style.backgroundImage = "url('"+this._images[0][1].src+"')";
    }
}

// AUTOFILL
FWC_sSelect.prototype.titleKeyUp = function(e)
{   var str = this.title.value.toLowerCase();
    var z = 0;
    var a = this.optionsDiv.childNodes;
    var pos;
    for(var i=0;i<a.length;i++)
    {   var s = a[i];
        if(s.nodeType == 1)
        {   s.style.display = 'none';
            var opts = s.getAttribute('val') ? s : false || $(s).descendants().find(function(n) {return n.getAttribute('val')});
            if(opts)
            {   pos = this.optionsNodes.indexOf(opts);
                if(opts.firstChild.firstChild.nodeValue.toLowerCase().indexOf(str) != 0 || opts.firstChild.firstChild.nodeValue.toLowerCase() == str)
                {   s.style.display = 'none';
                    this.shownOptions[pos] = '';
                }
                else
                {   s.style.display = '';
                    this.shownOptions[pos] = opts;
                    z++;
                }
            }
        }
    }
    this.hid = true
    if(z == 0)
        this.hideOptions(true);
    else
        this.showOptions(true);
}

FWC_sSelect.prototype.titleKeyDown = function(e)
{   if(e.keyCode == '38' || e.keyCode == '40' || e.keyCode == '36' || e.keyCode == '35')
    {  e.cancelBuble = true; this.navigateOptions(e.keyCode); return false; }    // 'up' or 'down' key
    if(e.keyCode == '13')
    { e.stopPropagation?e.stopPropagation():(e.cancelBubble=true); e.preventDefault?e.preventDefault():(e.returnValue=false); this.hitOption(); return false; }                   // 'enter' key
}

FWC_sSelect.prototype.titleBlur = function()
{   if((this.edited || this.title.value == '') && !this.readonly)
    {   var owntype = true;
        var t = this;
        var simOpt = this.optionsNodes.find(function(s) {return s.innerHTML.stripTags().toLowerCase() == t.title.value.toLowerCase()})
        if(simOpt)
        {   owntype = false;
            this.selectOption(false);
            return this.selectOption(simOpt);
        }
        if(owntype && this.ownvalues)
        {   this.selectOption(false);
        }
        this.edited = false;
        if(!simOpt) this.setHValue(true);
    }
    else return true;
}

// sets VALUE
FWC_sSelect.prototype.setHValue = function(auto,fst)
{   if(this.value[0] == null) { this.value = this.value.without(null); }
    var val = (this.value.length > 0) ? this.value : (this.ownvalues ? (this.title.value || null) : null);
    this.value = (val instanceof Array) ? val : [val];
    if(!this.edited || !auto) this.setTitle(auto);
    if(!!this.onchange && !auto) return this.onchange();
}

// sets value of TITLE INPUT
FWC_sSelect.prototype.setTitle = function(auto)
{   var _title = [];
	if(this.value[0] == null) return; //_title.push(this.selectTitle);
    else if((typeof(this.fewprefix) == 'string') && (this.value.length > 1)) _title = this.fewprefix + ((this.fewall && this.value.length >= this.optionsNodes.length) ? this.fewall : this.value.length);
    else
    {   for(var i=0;i<this.optionsNodes.length;i++)
        {   if(this.value.indexOf(this.optionsNodes[i].getAttribute('val')) != -1 && this.optionsNodes[i].className.include('_Sel'))
                _title.push(this.optionsNodes[i].innerHTML.stripTags());
        }
    }
    if((this.fewall) && (this.value.length >= this.optionsNodes.length) && (typeof(_title) != 'string')) _title = this.fewall;
    if(!_title || _title.length == 0) _title = this.optionsNodes.first() && !this.ownvalues ? this.optionsNodes.first().innerHTML.stripTags() : this.value;

    this.title.value = _title instanceof Array ? _title.join(this.splitter) : _title;
}

// shows/hides OPTIONS DIV
FWC_sSelect.prototype.showHideOptions = function(act,noanim)
{   if(this.hid)
    {   $A(this.optionsDiv.childNodes).select(function(s) {return (s.nodeType == 1 && s.style.display == 'none')}).each(function(s) {return s.style.display = ''});
        this.hid = false;
    }
    this.shownOptions = this.optionsNodes.slice(0);
    if(act == 'hide') this.hideOptions(noanim);
    else if(act == 'show') this.showOptions(noanim);
    else
    {   if(this.optionsDiv.getStyle('visibility') != 'hidden' && !FWC.hides[this.id])
            this.hideOptions(noanim);
        else
            this.showOptions(noanim);
    }
}

FWC_sSelect.prototype.showOptions = function(noanim)
{
   this.optionsDiv.show();
    this.optionsDiv.style.visibility = 'visible';
    this.setDimensions(false);
    var maxh = this.maxheight || 100;

    this.optionsDiv.style.height = 'auto';
    this.optionsDiv.style.height = ((this.optionsDiv.offsetHeight > maxh) && (maxh != 0)) ? maxh+'px' : 'auto';

    var _height = this.optionsDiv.offsetHeight;

    if(this.showspeed !== 0 && !noanim)
    {   var t = this;
        if(FWC.hides[this.id])
        {   clearInterval(FWC.hides[this.id]);
            FWC.hides[this.id] = false;
        }
        else
        {   this.optionsDiv.style.clip = "rect("+_height+'px '+this.optionsDiv.offsetWidth+'px '+_height+'px 0px)';
            this.optionsDiv.style.top = this.top - _height+'px';
        }
        FWC.shows[this.id] = setInterval(function() {FWC.show(t.optionsDiv,15,t.top)},this.showspeed);
    }
    else
    {   this.optionsDiv.style.clip = "rect(0px "+this.optionsDiv.offsetWidth+"px "+_height+"px 0px)";
        if(window.opera)
            this.optionsDiv.style.overflow = 'auto';
        else
            this.optionsDiv.style.overflowY = 'auto';
    }
}

FWC_sSelect.prototype.hideOptions = function(noanim)
{   if(window.opera)
        this.optionsDiv.style.overflow = 'hidden';
    else
        this.optionsDiv.style.overflowY = 'hidden';

    if(this.hidespeed !== 0 && !noanim)
    {   var t = this;
        var _height = this.optionsDiv.offsetHeight;
        if(FWC.shows[this.id])
        {   clearInterval(FWC.shows[this.id]);
            FWC.shows[this.id] = false;
        }
        else
           this.optionsDiv.style.clip = "rect(0px "+this.optionsDiv.offsetWidth+"px "+_height+"px 0px)";
        FWC.hides[this.id] = setInterval(function() {FWC.hide(t.optionsDiv,15,t.top-_height,t,'hideOptions2')},this.hidespeed);
    }
    else this.hideOptions2(noanim);
}

FWC_sSelect.prototype.hideOptions2 = function(auto)
{   var c = (this.optionsDiv.getStyle('visibility') != 'visible');
    this.optionsDiv.style.visibility = 'hidden';
    this.optionsDiv.style.top = this.top;
    this.optionsDiv.hide();

    this.highlightOption(false);
    if(this.singleActive) this.singleActive.className = this.singleActive.className.replace("_Free","_Sel");
    if(!!this.onclose && !c && !auto) return this.onclose();
}


// handler for mouseover/out events of OPTION NODE
FWC_sSelect.prototype.highlightOption = function(pos)
{   var opt = pos !== false ? this.getOptionNode(pos) : false;
    if(this.optionOver) this.optionOver.className = this.optionOver.className.replace("_Over","_Out");
    this.optionOver = opt;
    if(opt)
    {   opt.className = opt.className.replace("_Out","_Over");
        if(!this.multiple && this.singleActive)
            this.singleActive.className = this.singleActive.className.replace("_Sel","_Free");
    }
}

// navigates OPTIONS by 'up', 'down', 'home' and 'end' keys
FWC_sSelect.prototype.navigateOptions = function(k)
{   var dir = (k == '38') ? 'up' : 'down';
    var n = (k == '38') ? -1 : 1;
    var sh = this.shownOptions.without('');
    var opt = sh[0];
    for(var i=0;i<sh.length;i++)
    {   if(sh[i].className.indexOf("_Over") != -1)
        {   opt = sh[i+n]; break;
        }
    }
    if(k == '35') opt = sh.last();
    else if(k == '36') opt = sh.first();
    if(this.optionsDiv.getStyle('visibility') != 'visible' && this.mode != 'text') this.showOptions();
    if(this.shownOptions.indexOf(opt) >= 0 && this.shownOptions.indexOf(opt) < this.shownOptions.length)
    {   this.highlightOption(opt);
        this.optionsDiv.scrollTop = opt.offsetTop - this.optionsDiv.offsetHeight/2;
    }
    return false;
}

// handler for 'ENTER' keyup event of OPTION NODE
FWC_sSelect.prototype.hitOption = function()
{   if(this.optionOver) this.selectOption(this.optionOver);
    return false;
}


// ************ Client-API functions ************ //


// OPTION NODE selection
FWC_sSelect.prototype.selectOption = function(pos,sel,e)
{   var opt = pos !== false ? this.getOptionNode((!pos&&pos!==0)?this.singleActive:pos) : false;
    sel = sel || false;
    if(!opt)
    {   for(var i=0;i<this.optionsNodes.length;i++)
        {   this.optionsNodes[i].className = this.optionsNodes[i].className.replace("_Sel","_Free");
            this.optionsNodes[i].className = this.optionsNodes[i].className.replace("_Over","_Out");
        }
        this.value = this.value.clear();
    }
    else
    {   if(!this.multiple)
        {   if(opt != this.singleActive)
            {   if(sel=='no') return;
                if(this.singleActive) this.singleActive.className = this.singleActive.className.replace("_Sel","_Free");
                opt.className = opt.className.replace("_Free","_Sel");
                this.singleActive = opt;
                this.value = [opt.getAttribute('val')];
            }
            else
            {   var seled = opt.className.include('_Sel');
                if(seled && (!sel || sel == 'no'))
                {   opt.className = opt.className.replace("_Sel","_Free");
                    this.value = this.value.clear();
                }
                else if(!seled && (!sel || sel == 'yes'))
                {   opt.className = opt.className.replace("_Free","_Sel");
                    this.value = [opt.getAttribute('val')];
                }
                else return;
            }
            this.hideOptions();
        }
        else
        {   if(opt.className.indexOf('_Sel') != -1 && (!sel || (sel && sel=='no')))
            {   opt.className = opt.className.replace("_Sel","_Free");
                this.value.splice(this.value.indexOf(opt.getAttribute('val')),1);
                if(this.value.length == 0) this.title.value = '';
            }
            else if(opt.className.indexOf('_Free') != -1 && (!sel || (sel && sel=='yes')))
            {   opt.className = opt.className.replace("_Free","_Sel");
                if(this.value.indexOf(opt.getAttribute('val')) == -1) this.value.push(opt.getAttribute('val'));
            }
            if(e) { if(this.usectrlkey && !e.ctrlKey) this.hideOptions(); }
        }
    }
    this.setHValue();
}


FWC_sSelect.prototype.selectAll = function(sel)
{   sel = sel || false;
    this.value = this.value.clear();
    if(!sel || sel == 'yes')
    {   for(var i=0;this.optionsNodes.length>i;i++)
        {   this.optionsNodes[i].className = this.optionsNodes[i].className.replace('_Free','_Sel');
            this.value[this.value.length] = this.optionsNodes[i].getAttribute('val');
        }
    }
    else
    {   for(var i=0;this.optionsNodes.length>i;i++)
          this.optionsNodes[i].className = this.optionsNodes[i].className.replace('_Sel','_Free');
    }
    this.setHValue();
}

// JavaScript AJAX-loader
FWC_sSelect.prototype.loadOptions_JS = function(file,attr,vals,callback)
{   if(!file) { alert('FWC->SmartSelect:loadOptions_JS: undefined parameter "xmlfile"!'); return; };
    var xmldoc = dom.load_file(file);

    if(attr && vals)
    {   var sel = xmldoc.getElementsByTagName(FWC.ns+'option');
        for(var i=0;i<sel.length;i++)
        {   if(vals.indexOf(sel[i].getAttribute(attr)) == -1)
            {   sel[i].parentNode.removeChild(sel[i]);
                if(!window.ActiveXObject) i--;
            }
        }
    }
    this.appendOptions(xmldoc,callback);
    return false;
}

// PHP AJAX-loader
FWC_sSelect.prototype.loadOptions_PHP = function(xmlfile,xpath,phpfile,method,params,callback)
{   var t=this;
    xpath = xpath || (callback == 'autofill' ? 'fwc:option[contains(concat("|",text()),"|'+this.title.value+'")]' : 'fwc:option');
    if(!phpfile && !xmlfile) { alert('FWC->SmartSelect:loadOptions_PHP: default XML-parser needs a link for XML file!'); return; };

    new Ajax.Request((phpfile||FWC.fwcp+'fwc/php/ajaxload.php'), {
      method : method || 'post',
      parameters : Object.extend({rand:Math.random(),xmlfile:xmlfile,xpath:xpath},params||null),
      onSuccess : function(req) { t.appendOptions(req.responseXML?(req.responseXML.documentElement?req.responseXML:req.responseText):req.responseText,(callback=='autofill'?function(o){o.titleKeyUp()}:callback)); }
    });
    return false;
}

// returns the xpath-string contains all VALUES
FWC_sSelect.prototype.value2xpath = function(attr,val,end)
{   val = val || this.value;
    if(!attr) { alert('FWC->SmartSelect:value2xpath: undefined parameter: "attr"!'); return;}
    var xpath = "//*[@"+attr+"='"+((val[0]!=null)?val[0]:'some-Impossible_Value')+"'";
    for(var i=1;i<val.length;i++) xpath += " or @"+attr+"='"+val[i]+"'";
    xpath += (end?" "+end:"")+"]";
    return xpath;
}

// creates the INPUT:HIDDEN fields for each element of VALUE array
FWC_sSelect.prototype.value4form = function()
{   var cont = this.informDiv;
    cont.innerHTML = "";
    for(var i=0;i<this.value.length;i++)
    {   var ih = document.createElement("input");
        ih.setAttribute('type','hidden');
        ih.setAttribute('name',this.id+'[]');
        ih.value = this.value[i];
        cont.appendChild(ih);
    }
}

// add, remove & edit OPTION NODE
FWC_sSelect.prototype.addOption = function(arr,pos)
{   var t = this;

    if(!FWC.XSLs[this.design]) return FWC.loadCompactedXSL(this,function() {t.addOption(arr,pos);});
    else var xsldoc = FWC.XSLs_addAttrs(FWC.XSLs[this.design],this);

    var xmldoc = dom.load_string('<fwc:select id="sselect" xmlns:fwc="http://alx.vingrad.ru/fwc" />');
    var xml = FWC.buildOption(arr.evalJSON(),xmldoc);
    xmldoc.documentElement.appendChild(xml);

    var html = dom.xslt(xmldoc,xsldoc).replace(/<\/?(fwc:)?select[^>]*>/gi,'');

    var opt = pos !== undefined ? this.getOptionNode(pos) : false;
    if(opt)
    {   new Insertion.Before(opt,html);
        this.optionsNodes.splice(this.optionsNodes.indexOf(opt),0,opt.previous());
        this.shownOptions.splice(this.optionsNodes.indexOf(opt),0,opt.previous());
    }
    else
    {   new Insertion.Bottom(this.optionsDiv,html);
        this.optionsNodes.push(this.optionsDiv.lastChild);
        this.shownOptions.push(this.optionsDiv.lastChild);
    }
    this.setHValue(true);
}

FWC_sSelect.prototype.removeOption = function(pos)
{   var opt = this.getOptionNode(pos);
    opt.parentNode.removeChild(opt);
    this.shownOptions.splice(this.optionsNodes.indexOf(opt),1);
    this.optionsNodes = this.optionsNodes.without(opt);
    this.setHValue(true);
}

FWC_sSelect.prototype.editOption = function(arr,pos)
{   var opt = this.getOptionNode(pos);
    arr = arr.evalJSON();
    if(arr[0]) opt.firstChild.firstChild.nodeValue = arr[0];
    if(arr[1]) opt.setAttribute('val',arr[1]);
    for(var i in arr[2]) opt.setAttribute(i,arr[2][i]);
    this.setHValue(true);
}

// removes all OPTION NODES from OPTIONS DIV and appends new from xmldoc (AJAX-feature)
FWC_sSelect.prototype.appendOptions = function(data,callback)
{   var t = this;
    var xmldoc, xsldoc;
    if(typeof(data) == 'string')
    {   data = data.evalJSON(true);
        xmldoc = dom.load_string('<fwc:select id="sselect" xmlns:fwc="http://alx.vingrad.ru/fwc" />');
        for(var i=0;i<data.length;i++)
            xmldoc.getElementsByTagName(FWC.ns+'select')[0].appendChild(FWC.buildOption(data[i],xmldoc));
    }
    else
        xmldoc = data;
    if(!FWC.XSLs[this.design]) {return FWC.loadCompactedXSL(this,function() {t.appendOptions(xmldoc,callback);});}
    else xsldoc = FWC.XSLs_addAttrs(FWC.XSLs[this.design],this);
    var xhtml = dom.xslt(xmldoc,xsldoc).replace(/<\/?[^\?>]+>/i,'');
    this.optionsDiv.innerHTML = xhtml;
    this.initOptions();
    if(callback) callback(this);
}

//-->

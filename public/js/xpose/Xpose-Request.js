/** 
 * 
 * 
 * 
 */
//This should be dynamic
//const XposeWebBaseUrl = 'http://localhost:8080/HRMS/wp-content/plugins/HRMS/api/';

if(!window.XposeWebBaseUrl)
{
  window.XposeWebBaseUrl = '';
}
var LoadQueueCount = 100;
setInterval(function(){ LoadQueueCount = 100; }, 3000);

window.FileRequest = function(url, formdata, func){
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      func( ParseStringToObject('application/json', xhttp.responseText));
    }
    else{
      func( ParseStringToObject('application/json', xhttp.responseText));
    }
  }
  xhttp.open('POST', XposeWebBaseUrl + url, true);
  xhttp.setRequestHeader('Content-Type', 'multipart/form-data');
  xhttp.setRequestHeader('LoginID', getCookie('LoginID') || 0);
  var xcsrftoken = document.querySelector('meta[name="csrf-token"]');
  if(xcsrftoken)
  {
    xcsrftoken = xcsrftoken.getAttribute('content');
    if(xcsrftoken)
      xhttp.setRequestHeader('X-CSRF-TOKEN', xcsrftoken);
  }
  xhttp.send(formdata);

}
window.FileAxiosRequest = function(url, formData, func){  
    axios({
    method: "POST",
    url: url,
    data: formData,
    headers: {
        "Content-Type": "multipart/form-data"
    }
    }).then(res=>{
      func(res.data)
    });
}
window.FileAxiosRequest2 = function(url, formData){  
    return axios({
    method: "POST",
    url: url,
    data: formData,
    headers: {
        "Content-Type": "multipart/form-data"
    }
    });
}

window.WebRequest2 = function(_method, _url, _content = null, _contentType ='application/json'  ){
  //var request  = ParseObjectToString(_contentType, _content) ;
  var request  = _content ? ParseObjectToString(_contentType, _content) : ( _method.toUpperCase() == 'POST' ? '{}' :  null);
  var xcsrftoken = document.querySelector('meta[name="csrf-token"]');
  if(xcsrftoken)
  {
    xcsrftoken = xcsrftoken.getAttribute('content');
  }
  return fetch(_url, { method:_method, body: request, credentials: 'include', headers:{'Content-Type': _contentType, 'X-CSRF-TOKEN': xcsrftoken, 'Accept':'application/json'}});
  /*
  .then(response => {
      if (response.ok) 
          return response.json()
      //Initiator to stop from closing
      Swal.showValidationMessage( `ERROR` )
      response.json().then(a=>{
          throw new Error(a.message);
      })
      .catch(error => { 
          Swal.showValidationMessage(
          `Request failed: ${error}`
          )
      }); 
  })*/
}

window.WebRequest = function(method, url, data, contentType, func, isRaw = false)
{
    var xhttp = new XMLHttpRequest();
    setTimeout( function(){
      xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
          
            // Typical action to be performed when the document is ready:
            //console.log(xhttp.responseText, 'outpt');
            if(isRaw)
                func(xhttp.responseText);
            else
                func( ParseStringToObject(contentType, xhttp.responseText));
              if(xhttp.PreloadFlags)
              {
                xhttp.PreloadFlags();
              }
          }
      };
      xhttp.open(method, XposeWebBaseUrl + url, true);
      xhttp.setRequestHeader('Content-Type', contentType);
      xhttp.setRequestHeader('LoginID', getCookie('LoginID') || 0);
      var xcsrftoken = document.querySelector('meta[name="csrf-token"]');
      if(xcsrftoken)
      {
        xcsrftoken = xcsrftoken.getAttribute('content');
        if(xcsrftoken)
          xhttp.setRequestHeader('X-CSRF-TOKEN', xcsrftoken);
      }
      //console.log(xcsrftoken);
      
      //FORMATTING REQUEST
      var request  = ParseObjectToString(contentType, data);
      //console.log(request);
      
      xhttp.send(request);
    }, LoadQueueCount);
    LoadQueueCount += 300;
    return xhttp;
}

////////////PARSERS//////////////////
window.ParseStringToObject = function(contentType, data)
{
  try
  {
    contentType = contentType.toLowerCase();
    
    if(contentType == "application/json")
        return JSON.parse(data);
        
    if(contentType == "application/xml" || contentType == "text/xml")
    {
        return   cleanXML2JSON(data);
    }
    return data;
  }catch(e){
    console.error('ERROR parsing, api got bad output', data);
    return data; 
  }
}

window.ParseObjectToString = function(contentType, data)
{
    if(data == null)
        return null;
    
    if(contentType == "application/json")
    {
        if(data.constructor === " ".constructor)
        {
            return data;
        }
        else if(canJSON(data))
        {
            return JSON.stringify(data);
        }
        return data;
    }
    else if( canJSON(data))
    {
        var Obj = null;
        
        if(data.constructor === " ".constructor)
        {
            Obj = JSON.parse(data);
        }
        return OBJtoXML(Obj);
    }
    return null;
}

window.OBJtoXML = function(obj) {
  var xml = '';
  for (var prop in obj) {
    xml += obj[prop] instanceof Array ? '' : "<" + prop + ">";
    if (obj[prop] instanceof Array) {
      for (var array in obj[prop]) {
        xml += "<" + prop + ">";
        xml += OBJtoXML(new Object(obj[prop][array]));
        xml += "</" + prop + ">";
      }
    } else if (typeof obj[prop] == "object") {
      xml += OBJtoXML(new Object(obj[prop]));
    } else {
      xml += obj[prop];
    }
    xml += obj[prop] instanceof Array ? '' : "</" + prop + ">";
  }
  var xml = xml.replace(/<\/?[0-9]{1,}>/g, '');
  return '<xml>'+xml+'</xml>'
}
window.canJSON = function(value) {
    try {
        
        JSON.stringify(value);
        return true;
    } catch (ex) {
        return false;
    }
}
window.parseXml=function(xml) {
   var dom = null;
   if (window.DOMParser) {
      try { 
         dom = (new DOMParser()).parseFromString(xml, "text/xml"); 
      } 
      catch (e) { dom = null; }
   }
   else if (window.ActiveXObject) {
      try {
         dom = new ActiveXObject('Microsoft.XMLDOM');
         dom.async = false;
         if (!dom.loadXML(xml)) // parse error ..

            window.alert(dom.parseError.reason + dom.parseError.srcText);
      } 
      catch (e) { dom = null; }
   }
   else
      alert("cannot parse xml string!");
   return dom;
}

window.cleanXML2JSON = function(data)
{
  var cleanNode = [];
  var nodes =  xmlToJson( parseXml( data ))["nodes"]["node"];
  if(Array.isArray(nodes))
  {
    cleanNode = nodes;
  }
  else 
    cleanNode.push(nodes);
  //HasCleanup
  
  
  console.log(JSON.stringify(cleanNode));
  
  return cleanNode;
}

window.xmlToJson = function(xml) {
	
	// Create the return object
	var obj = {};

	if (xml.nodeType == 1) { // element
		// do attributes
		if (xml.attributes.length > 0) {
		obj["@attributes"] = {};
			for (var j = 0; j < xml.attributes.length; j++) {
				var attribute = xml.attributes.item(j);
				obj["@attributes"][attribute.nodeName] =  attribute.nodeValue;
			}
		}
	} else if (xml.nodeType == 3) { // text
		obj = xml.nodeValue;
	}

	// do children
    
	if (xml.hasChildNodes()) {
		for(var i = 0; i < xml.childNodes.length; i++) {
			 var item = xml.childNodes.item(i);
			 var nodeName = item.nodeName;
            
             if(item.constructor.name == "Element")
             {
                try{
                
                if(item.childNodes.item(0).constructor.name == "Text")
                {
                    console.log(item.childNodes.item(0).constructor.name);
                    obj[nodeName] = item.childNodes.item(0).nodeValue;
                    continue;
                }
                }catch(e)
                {
                    console.log(e);
                }
             }
             
            if (typeof(obj[nodeName]) == "undefined") {
                	obj[nodeName] = xmlToJson(item);
			} else {
				if (typeof(obj[nodeName].push) == "undefined") {
					var old = obj[nodeName];
					obj[nodeName] = [];
					obj[nodeName].push(old);
				}
				obj[nodeName].push(xmlToJson(item));
			}
		}
	} 
       
	return obj;
}
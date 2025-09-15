window.LoginSend = function(ev, toSendData, isSend, dataval)
{
    if(!isSend)
    {
        console.log(toSendData);
    }  
    else if(isSend)
    {
        console.log(dataval);
    }
}

//[sys-filter-loads] - a function used to load for initiating list for search;
//[sys-search-function] - a function used to request.
window.SearchInputViewLoad = function(el)
{
    if(!el.hasSet)
        el.hasSet = true;
    else return;
    var dummyDIV = document.createElement('DIV');
    //dummyDIV.classList.add('col-sm-12');
    dummyDIV.classList.add('w3school-input-group');
    var filterloads = el.getAttribute('sys-filter-load') ? 'sys-load="'+el.getAttribute('sys-filter-load')+'"' : '';
    var filterEnterTrigger = el.getAttribute('sys-search-function') ? 'sys-search-function="'+el.getAttribute('sys-search-function')+'"' : '';

    //dummyDIV.innerHTML = '<input type="text" style="float: right;" class="w3searchbox header-table-searchbox" placeholder="Name, property:value, keywords.., etc.">';
    dummyDIV.innerHTML = '<div class="input-group"> <input type="text" class="form-control w3searchbox" '+filterEnterTrigger+' style="float:right; margin:0px;"><span class="input-group-addon" style="border: 2px solid #ccc; border-left:0px; border-top-right-radius:4px; border-bottom-right-radius:4px;"><i class="fa fa-bars" style="margin:0px; cursor: pointer;"></i></span> </div>'+
                        '<div style="float:right;">'+
                            '<div class="filter-lists hide" style="right:4%; background-color:white; border: 1px dotted black; padding: 5px; border-radius: 4px;"'+filterloads+'></div>'+
                        ' </div>';
    el.append(dummyDIV);
    dummyDIV.querySelector('.w3searchbox').placeholder = el.getAttribute('sys-search-placeholder') ?? '';
    //console.log(el);

    dummyDIV.querySelector('.w3searchbox').onkeypress = function(evt)
    {
        if(evt.keyCode == 13)
        {
            var filterEnterTrigger = evt.target.getAttribute('sys-search-function');
            if(filterEnterTrigger)
            {
                window[filterEnterTrigger](evt.target);
            }
        }
    }
    dummyDIV.querySelector('.fa-bars').onclick = function(evt)
    {
        evt.target.closest('.w3school-input-group').querySelector('.filter-lists').classList.toggle('hide');
    }

}

window.CreateSearchFilterBtn = function( targetSelector, nameInfo = {ID:0, Column:"Column" ,Text:"Sample" }, bclass = [], createTrigger = function(el){}, deleteTrigger = function(evt){}){
   // var buttonSample = 
   //<label class="search-filter-btn bg-blue"> Factory: PRD <a class="fa fa-close" ></a> </label>
   //Preparing for Display
   var searchLabel = document.createElement('LABEL');
   searchLabel.innerHTML = nameInfo.Column+': '+nameInfo.Text+ ' <a class="fa fa-close" ></a>';
   searchLabel.Data = nameInfo;

   searchLabel.classList.add("search-filter-btn");
   bclass.forEach((classItem, index)=>{
        if(!searchLabel.classList.contains(classItem))
            searchLabel.classList.add(classItem);
   });

   //appending
   if(targetSelector != null)
   {
     var _target = document.querySelector(targetSelector);
     if(_target)
     {
        //Existence check if exists it will not add.
        var exists = false;
        _target.querySelectorAll('.search-filter-btn').forEach((nodeEl, inde)=>{
            if(nodeEl.Data.ID == nameInfo.ID && nodeEl.Data.Column == nameInfo.Column)
            {
                exists = true;
            }
        }); 
        if(exists)
            return;


         _target.append(searchLabel);
         if(createTrigger!= null)
            createTrigger(searchLabel);
     }
   }

   if(deleteTrigger!= null)
    searchLabel.querySelector('a.fa-close').onclick = function(evt){
        evt.target.closest('.search-filter-btn').remove();
        deleteTrigger(evt);
    } 



   return searchLabel;

}

window.CreateTableFooter = function(el){
    //<tr><td colspan="10"><div class="table-footer-container" style="margin: 0px 5px; color:black;"><div style="display:inline-block;">ItemsFound: <label class="footer-itemsfound">7</label><!--<button class="ghbutton">Filter</button><button class="ghbutton">Columns</button>--></div><div style="float:right;display:block;">Item per Page:<select class="footer-itemperpage"><option value="20">20</option><option value="50">50</option><option value="100">100</option></select><button class="footer-backbutton ghbutton icon arrowleft">Back</button> Page: <input class="footer-pageno" value="1" style="width: 40px; text-align: center;"> / <label class="footer-totalpage">1</label> <button class="footer-nextbutton ghbutton icon arrowright">Next</button></div></div></td></tr>
    if(!el.hasSet)
        el.hasSet = true;
    else return;
    
    var trElement = document.createElement('TR');
    trElement.innerHTML = '<td colspan="10"><div class="table-footer-container" style="margin: 0px 5px; color:black;"><div style="display:inline-block;">ItemsFound: <label class="footer-itemsfound">??</label><!--<button class="ghbutton">Filter</button><button class="ghbutton">Columns</button>--></div><div style="float:right;display:block;">Item per Page:<select class="footer-itemperpage"><option value="20">20</option><option value="50">50</option><option value="100">100</option></select><button class="footer-backbutton ghbutton icon arrowleft">Back</button> Page: <input class="footer-pageno" value="1" style="width: 40px; text-align: center;"> / <label class="footer-totalpage">1</label> <button class="footer-nextbutton ghbutton icon arrowright">Next</button></div></div></td>';
    el.append(trElement);
}

window.ButtonSwitchClick = function(ev){

    var par = ev.target.parentElement;
   par.querySelectorAll('.button-switch').forEach((nodeEl, index)=>{
        //console.log(nodeEl);
        if(par == nodeEl.parentElement){
            if(ev.target == nodeEl)
                nodeEl.classList.add('active');
            else
                nodeEl.classList.remove('active');
        }
   });


}

window.CSVToArray = function( strData, strDelimiter ){
    // Check to see if the delimiter is defined. If not,
    // then default to comma.
    strDelimiter = (strDelimiter || ",");

    // Create a regular expression to parse the CSV values.
    var objPattern = new RegExp(
        (
            // Delimiters.
            "(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +

            // Quoted fields.
            "(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +

            // Standard fields.
            "([^\"\\" + strDelimiter + "\\r\\n]*))"
        ),
        "gi"
        );


    // Create an array to hold our data. Give the array
    // a default empty first row.
    var arrData = [[]];

    // Create an array to hold our individual pattern
    // matching groups.
    var arrMatches = null;


    // Keep looping over the regular expression matches
    // until we can no longer find a match.
    while (arrMatches = objPattern.exec( strData )){

        // Get the delimiter that was found.
        var strMatchedDelimiter = arrMatches[ 1 ];

        // Check to see if the given delimiter has a length
        // (is not the start of string) and if it matches
        // field delimiter. If id does not, then we know
        // that this delimiter is a row delimiter.
        if (
            strMatchedDelimiter.length &&
            strMatchedDelimiter !== strDelimiter
            ){

            // Since we have reached a new row of data,
            // add an empty row to our data array.
            arrData.push( [] );

        }

        var strMatchedValue;

        // Now that we have our delimiter out of the way,
        // let's check to see which kind of value we
        // captured (quoted or unquoted).
        if (arrMatches[ 2 ]){

            // We found a quoted value. When we capture
            // this value, unescape any double quotes.
            strMatchedValue = arrMatches[ 2 ].replace(
                new RegExp( "\"\"", "g" ),
                "\""
                );

        } else {

            // We found a non-quoted value.
            strMatchedValue = arrMatches[ 3 ];

        }


        // Now that we have our value string, let's add
        // it to the data array.
        arrData[ arrData.length - 1 ].push( strMatchedValue );
    }

    // Return the parsed data.
    return( arrData );
}
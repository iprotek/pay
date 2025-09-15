/*!
 * Xpose.js
 * Version - 1.0.0
 * Licensed under the MIT license - https://opensource.org/licenses/MIT
 *
 * Copyright (c) 2020 Joseph Aguilar
 * drimcaster@gmail.com
 */
       
       
/**
 * USAGE:
 ELEMENT ATTRIBUTES
 [sys-click]    - call a function(ev) when clicked
 [sys-load]     - call a function(el) when loaded
 [sys-change]

 
 ELEMENT DATA SOURCE ATTRIBUTES
 [data-source]  - reference an object data
 [data-bind]    - reference to a specific data-source property name
 [data-source-dynamic] - make a new object and make it as reference.
 [data-bind-property] - element property to be for data-source data-bind value.

ELEMENT VIEWS SETTINGS
[sys-view]      - formview | cellview | tableview
[sys-view-mode] - * | addonly | remove | edit


 ELEMENT FORM VIEWS 
 [sys-control]  - command or control for views
 [sys-buttons]  - * | save | add | cancel | reset | saveas
 [sys-save-click] = view clicking save
 [sys-edit-mode]
 [sys-cancel-click]
 [sys-add-load]
 [sys-edit-load]
 [sys-remove-load]


 TABLE VIEW
 [sys-filter-click]
 [sys-additem-click]
 [sys-search-click]
 [sys-clear-click]

 VIEW LOADING
 [sys-click-view]
 [sys-click-view-target] 
 [sys-click-view-animate]
 [sys-view-onload]      - Function call when the loaded
 [sys-view-load]        - Name of view

 LOADING
 [sys-preloading] - prevent button to make multiple request when the request is not yet completed.
 [sys-submit] - with preloading and cannot multiple request.
 [sys-submit-method] - POST | GET | PUT | DELETE
 [sys-submit-url] - '/v2/something.'

 SELECT LIST
 * IF AppList if available.
 [sys-applist]
 [sys-applist-display]
 [sys-applist-value]
 [sys-datalist]
 [sys-list-display]
 [sys-list-value]
 [sys-list-displaytype] - value(default) or properties 


 JAVASCRIPT CODE
    el.DynamicDataSourceLoad = function(el) is called everytime you data source dynamic is loaded.
    ElementClone(el, function(el) );
    SetElementBindingSource(el, newSource);
    ResetElementBindingSource(el);    
 
 //CONTROLLER
 [sys-controlaccess]
 * 
 */



var CurrentAppIndex = 0;
var AppList = [];
var ViewResourcesData = {};
var AppStartReady = false;
var ViewResourcesReady = false;
var EditMode = true;
var StartRequestReady = true;
const XposeStart = new Event('AppStart');
        
var dataBind =
{
    Name: "Sample"
};
        
        //APPLICATION SETTINGS
        
function CurrentApp() {
    return AppList.Data[CurrentAppIndex];
}

function AppListLoad(fn)
{
    /*
    WebRequest('GET','v2/AppDataList/0/0/0', null, "application/json", function(e){
        console.log(e.Data[0]);
        AppList = e.Data[0];
        DataListLoad();
        if(fn)
        {
            fn(e);
        }
    });
    */
}
function ViewResourcesDataLoad(fn)
{
    /*
    WebRequest('GET','view/',null, 'application/json', function(e){
        ViewResourcesData = e;
        if(fn)
        {
            fn(e);
        }  
    });
    */

}
        
        
function AppStart() {
    AppStartReady = true;
    ViewResourcesReady = true;
    AppLoad();
    /*
    if(StartRequestReady)
    {
        //APP INFOS
        //AppListLoad(function(res){
        //  AppStartReady = true;
            //AppLoad();
        //});
        
        
        //RESOURCES
        ViewResourcesDataLoad( function(e){
            ViewResourcesReady = true;
            AppLoad();            
        });
        //AppStartReady = true;
        //ViewResourcesData = true;
        //AppLoad();
    }
    else
    {
        ViewResourcesReady = true;
        AppLoad();
    }
    */
}
        
function AppLoad() {
    //!AppStartReady || 
    if (!ViewResourcesReady) return;

    //Executing all function in the window.addEventListener('AppStart', <func>, false);
    window.dispatchEvent(XposeStart);
    //console.log(ViewResourcesData);

    //SETTING HERE
    ElementLoadSetup(document);

    //LoadDataSourceBindingElements('dataBind_0');
    MutationObserved(document);
}
        

        
        /*
            INTERNAL / UNCOMMON USED FUNCTIONS 
        */      
window.addEventListener('load', function () {
    //LOAD APP INFOS
    AppStart();

});
        
window.addEventListener('click', function (e) {

    //Clicking with attribute
   return SystemClick(e);

});
//Trigger Setup Input

        
document.addEventListener('input', function (evt) {
    //Getting the Value
    //console.log( evt.target.value );

    //detects the bind
    var el = evt.target;
    //check the Binding attribute first
    if (el.getAttribute('sys-change')) {
        var inputchange = el.getAttribute('sys-change');
        if (typeof (window[inputchange]) == 'function') {
            window[inputchange](evt);
        }
    }

    //THIS IS FOR UPDATING THE DATASOURCE
    var datasource = el.getAttribute('data-source');
    if (!datasource) return;
    if (!isObject(datasource)) return;


    //Check if databind is a function
   var canSet = ObjectCanGetSet(window[datasource], el.getAttribute('data-bind'))
    if(canSet == 0 || canSet == 1)
    {
        console.error(" Non Setter function.");
        return;
    }

    if(typeof(window[datasource][el.getAttribute('data-bind')]) == 'function')
    {
        console.error('Cannot update get function.');
        return;
    }

    //Update The DataSource
    if(evt.target.type == 'checkbox')
    {
        //window[datasource][el.getAttribute('data-bind')] = evt.target.checked;
        setObjectValue(window[datasource], el.getAttribute('data-bind'), evt.target.checked);
    }
    else if(evt.target.type == 'file'){ return;}
    else
    {
        //window[datasource][el.getAttribute('data-bind')] = evt.target.value;
        setObjectValue(window[datasource], el.getAttribute('data-bind'), evt.target.value);
    }
    //console.log( typeof( window[el.getAttribute('data-source')]), window[el.getAttribute('data-source')] );

    //Update the other element using the same data-source..
    ElementSourceBindTrigger(el, datasource, el.getAttribute('data-bind'));

});

//-1: A property or Non existent property
//0 : NO GET NOR SET
//1 : GET ONLY
//2 : SET ONLY
//3 : GET AND SET
function ObjectCanGetSet(objectData, propertyName)
{
    //Segration of function from entries
    var allprops = Object.getOwnPropertyNames(objectData);
    var allkeys = Object.keys(objectData);
    var getfunc = allprops.filter( (item) => {  return allkeys.indexOf(item) < 0; } );
    
    if(getfunc.indexOf(propertyName) >= 0)
    {
        var canGet = false;
        var canSet = false;
        if (!getObjectValue(objectData, propertyName).hasOwnProperty('get'))
        {
            canGet = true;
        }
        else if (!getObjectValue(objectData, propertyName).hasOwnProperty('set'))
        {
            canSet = true;
        }
        if(canGet && canSet)
            return 3;
        if(canSet)
            return 2;
        if(canGet)
            return 1;
        return 0;
    }
    return -1;
}
        
function MutationObserved(targetNode) {
    // Select the node that will be observed for mutations
    //const targetNode = document.getElementById('some-id');
    //if(!AppStartReady) 

    // Options for the observer (which mutations to observe)
    const config = { attributes: true, childList: true, subtree: true };

    // Callback function to execute when mutations are observed
    const callback = function (mutationsList, observer) {
        // Use traditional 'for loops' for IE 11
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList') {
                //console.log('A child node has been added or removed.');
                for (var i = 0; i < mutation.addedNodes.length; i++) {
                    var el = mutation.addedNodes[i];
                    //console.log(el);
                    if (!el.tagName) continue;
                    //console.log(el);
                    ElementLoadSetup(el);
                    //if(!el.getAttribute('sys-load')) continue;

                    //Trigger Load Function
                    //ElementLoadTrigger(el, el.getAttribute('sys-load'));
                }
            }
            else if (mutation.type === 'attributes') {
                //console.log('The ' + mutation.attributeName + ' attribute was modified.');
            }
            //console.log(mutation);
        }
    };

    // Create an observer instance linked to the callback function
    const observer = new MutationObserver(callback);

    // Start observing the target node for configured mutations
    observer.observe(targetNode, config);

    // Later, you can stop observing
    //observer.disconnect();
}
        
//AUTO UPDATE THE VALUE OF THE ELEMENTS WITHIN THE DATASOURCE EVERYTIME YOU MAKE AN INPUT
function ElementSourceBindTrigger(el, datasource, databind) {//[data-bind=' + databind + ']
    document.querySelectorAll('*[data-source=' + datasource + ']').forEach(function (node) {
        if (el != node) {
            var nodebind = node.getAttribute('data-bind');
            if(!nodebind) return;
            var bindproperty = '';
            if(node.type == 'checkbox')
                bindproperty =  node.getAttribute('data-bind-property') || 'checked';
            else
                bindproperty = node.getAttribute('data-bind-property') || 'value';
            
            node[bindproperty] = getObjectValue(window[datasource], nodebind );//window[datasource][nodebind];
            //console.log(window[datasource][databind]);
        }
            //console.log(el.type);
    });
}

function SysTokenLoader(el)
{
    var targetID =  el.getAttribute('sys-id');
    if(!targetID)
        targetID = 0;
    WebRequest('GET', 'v2/TokenKeys/'+targetID,null, 'application/json',function(resData){
        //console.log(resData);
        if(resData.Data.length){
            el.value = resData.Data[0].Token;
            el.setAttribute('value', resData.Data[0].Token);
        }
    });
}
        
        
function SetDynamicSource(el) {
    var datasource = el.getAttribute('data-source');

    //remove attribute when done dynamic.
    el.removeAttribute("data-source-dynamic");

    //GenerateDynamicSource that does not exist anywhere..
    var uniqueSource = datasource;
    var i = 0;
    while (true) {
        uniqueSource = datasource + '_' + i;
        if (document.querySelectorAll('*[data-source=' + uniqueSource + ']').length == 0) {
            if (typeof (window[uniqueSource]) == 'undefined') {
                window[uniqueSource] = {};
                break;
            }
        }
        i++;
    }

    //Update the children
    el.querySelectorAll('*[data-source=' + datasource + ']').forEach(function (node) {
        node.setAttribute('data-source', uniqueSource);
    });

    //
    el.setAttribute("data-source", uniqueSource);
    if (el.DynamicSourceLoad) {
        el.DynamicSourceLoad(el);
    }

    return uniqueSource;
}
        
//TRIGGER EVERYTIME WHEN AN ELEMENT LOADED
function ElementLoadSetup(el) {
    
    //ELEMENT THAT HAS sys-controlaccess SHOULD BE PRIORITIZED
    if(AppList && AppList.UserControlAccess)
    {
        if(el !=document)
        {
            if(el.getAttribute('sys-controlaccess'))
            {
                if(AppList.UserControlAccess[el.getAttribute('sys-controlaccess')] == '0')
                {
                      el.remove();
                    return;
                }
            }
        }
        el.querySelectorAll('*[sys-controlaccess]').forEach(function(node){
            if(AppList.UserControlAccess[node.getAttribute('sys-controlaccess')] == '0')
            {
                node.remove();
                console.log('Hello');
            }
        });
    }




    if(el != document)
    {
        //PARENT LOADS
        if( el.getAttribute('sys-load'))
        {
            ElementLoadTrigger(el, el.getAttribute('sys-load'));
        }
    }

    //CHILD LOADS
    el.querySelectorAll('*[sys-load]').forEach(function (node) {
        //console.log(node);
        if (!node.getAttribute('sys-load')) return;

        //Trigger Load Function
        ElementLoadTrigger(node, node.getAttribute('sys-load'));

    });


    //PARENT DYNAMIC DATA
    if(el != document)
    {
        if(el.hasAttribute('data-source-dynamic'))
        {
            if (!el.getAttribute('data-source')) {
                console.error(el, 'has no data-source');
                return;
            }
            SetDynamicSource(el);
        }
    }

    //LOAD FOR Dynamic Data
    el.querySelectorAll('*[data-source-dynamic]').forEach(function (node) {
        if (!node.getAttribute('data-source')) {
            console.error(node, 'has no data-source');
            return;
        }
        //console.log(node);

        SetDynamicSource(node);
    });

    //LiST DATA [sys-applist]
    if(el != document)
    {
        if(el.tagName == 'SELECT')
        {
            if(!el.hasAttribute('sys-applist-complete'))
            {
                //sys-applist
                var sysList = el.getAttribute('sys-applist');
                //sys-applist-display
                var sysListDisplay = el.getAttribute('sys-applist-display') || 'Name';
                //sys-applist-value
                var sysListValue = el.getAttribute('sys-applist-value') || 'ID';
                if(sysList)
                    SetSelectOptionsFromAppDataList(el, sysList, sysListDisplay , sysListValue );
            }
            if(!el.hasAttribute('sys-list-complete'))
            {
                var dataList = el.getAttribute('sys-datalist');
                var sysListDisplay = el.getAttribute('sys-list-display') || 'Name';
                var sysListValue = el.getAttribute('sys-list-value') || 'ID';
                var sysDisptype = el.getAttribute('sys-list-displaytype');
                if(dataList)
                {  
                    SetSelectOptionsFromAppDataList(el, dataList, sysListDisplay , sysListValue, sysDisptype, 'DataList' );
                }
            }
        }
    } 
    el.querySelectorAll('select[sys-applist], select[sys-datalist]').forEach(function (node) {
                
        
        //sys-applist
            if(!node.hasAttribute('sys-applist-complete'))
            {
                var sysList = node.getAttribute('sys-applist');
                var sysListDisplay = node.getAttribute('sys-applist-display') || 'Name';
                var sysListValue = node.getAttribute('sys-applist-value') || 'ID';

                if(sysList)
                    SetSelectOptionsFromAppDataList(node, sysList, sysListDisplay , sysListValue );
               
            }
            if(!node.hasAttribute('sys-list-complete'))
            {
                var dataList = node.getAttribute('sys-datalist');
                var sysListDisplay = node.getAttribute('sys-list-display') || 'Name';
                var sysListValue = node.getAttribute('sys-list-value') || 'ID';
                var sysDisptype = node.getAttribute('sys-list-displaytype') || 'value';

                if(dataList)
                {  
                    SetSelectOptionsFromAppDataList(node, dataList, sysListDisplay , sysListValue, sysDisptype, 'DataList' );
                }
            }


    });

    //VIEW LOADING
    if(el != document)
    {
        setTimeout(function(){
        SystemViewLoad(el)}, 10);
    }
    el.querySelectorAll('[sys-view-load]').forEach(function (node) {
        setTimeout(function(){
        SystemViewLoad(node)}, 10);
    });



    //LOAD FOR EDIT MODE
    if (EditMode == true) {
        //console.log(EditMode);
        //Append Form View

    }
}

function getObjectValue( obj, st) {
    return st.replace(/\[([^\]]+)]/g, '.$1').split('.').reduce(function(o, p) { 
        return o[p];
    }, obj);
}
function setObjectValue(obj, st, value){
    //var last = null;
    var setHolder = st.replace(/\[([^\]]+)]/g, '.$1').split('.');
    var setLength = setHolder.length;
    var setCount = 0;
    setHolder.reduce(function(o, p) { 
            //console.log(p);
            setCount++;
            if(setLength == setCount)
                o[p] = value;
        return o[p];
    }, obj);
    //last = value;
}

function SetSelectOptionsFromAppDataList(el, _AppDataListName, sysListDisplay = 'Name', sysListValue = 'ID', displayType = 'value', listType = 'AppList')
{
    //Object.entries
    var myOptionList = null;
    
    if(listType == 'AppList')
    {
        myOptionList = getObjectValue(window.AppList,_AppDataListName);//AppList[AppDataListName];
        el.setAttribute('sys-applist-complete','1');
    }
    else
    {
        myOptionList = getObjectValue(window, _AppDataListName);
        el.setAttribute('sys-list-complete','1');
    }
    if(displayType == 'value')
    {
    //console.log(AppDataListName,AppList['HRMSUsageList']);
        if(myOptionList)
        {
            //REMOVING TO PREVENT READDING
            //should have an N/A
            var NAoption = document.createElement('option');
            NAoption.text = 'N/A';
            NAoption.value = 0;
            el.add(NAoption);
            for(var i =0; i< myOptionList.length; i++)
            {
                var optionData = myOptionList[i];
                var option = document.createElement("option");
                option.Data = optionData;
                option.text = optionData[sysListDisplay]; //sysListDisplay
                option.value = optionData[sysListValue]; //sysListValues
                el.add(option);
            }
        }
        else
        {
            console.error(el,'got null list');
        }
    }
    else
    {
        if(myOptionList)
        {
        
           var keyList =  [];
           if(Array.isArray(myOptionList))
               keyList = Object.keys(myOptionList[0]);
            else
                keyList = Object.keys(myOptionList);

            var NAoption = document.createElement('option');
            NAoption.text = 'N/A';
            NAoption.value = '';
            el.add(NAoption);
           for(var i =0; i< keyList.length; i++)
           {
               var optionData = keyList[i];
               var option = document.createElement("option");
               option.text = optionData; //sysListDisplay
               option.value = optionData; //sysListValues
               el.add(option);
           }

        }
    }
    
}

function TableFooterConstraints(tableFooter, fn)
{
    if(!tableFooter)return;
    if(tableFooter.Data != null) return tableFooter.Data;
    tableFooter.Data = {
        _ItemsFound : 0,
        _CurrentPage : 1,
        _ItemPerPage : 20
    }
    //console.log(fn);
    tableFooter.querySelector('.footer-itemperpage').onchange = fn;
    tableFooter.querySelector('.footer-pageno').onchange = function(){ 
        if(tableFooter.Data.TotalPage >= this.value && this.value >= 1 && this.value  != tableFooter.Data.CurrentPage)
        {
            //console.log(this.value);
            fn(this);
        }
    };

    //tableFooter.querySelector('.footer-itemsfound')
    Object.defineProperty(tableFooter.Data, "ItemsFound",{
        get:function(){
            return this._ItemsFound;
        },
        set: function(value){
            this._ItemsFound = value;
            tableFooter.querySelector('.footer-itemsfound').innerHTML = value;
            tableFooter.querySelector('.footer-totalpage').innerHTML = this.TotalPage;
        }
    });
    //tableFooter.querySelector('.footer-itemperpage')
    Object.defineProperty(tableFooter.Data, "ItemPerPage",{
        get:function(){
            return this._ItemPerPage || 20;
        },
        set: function(value){
            tableFooter.querySelector('.footer-itemperpage').value = value;
            this._ItemPerPage = value;
        }
    });
    //tableFooter.querySelector('.footer-pageno')
    Object.defineProperty(tableFooter.Data, "CurrentPage",{
        get:function(){
            return this._CurrentPage;
        },
        set: function(value){
            if(this.TotalPage >= value && value >= 1)
            {
                //if(this._CurrentPage != value)
                //{
                    tableFooter.querySelector('.footer-pageno').value = value;
                    tableFooter.querySelector('.footer-pageno').dispatchEvent(new Event('change'));
                    this._CurrentPage = value;
                //}
            }
        }
    });
    Object.defineProperty(tableFooter.Data, "TotalPage",{
        get:function(){
            var getPages = this._ItemsFound / this._ItemPerPage;
            var leftItems = this._ItemsFound - (this._ItemPerPage * getPages.toString().match(/^-?\d+(?:\.\d{0,0})?/)[0].replace('.',''));
            if(leftItems > 0)
            {
                getPages = getPages + 1;
            }
            if(getPages <= 0)
                getPages = 1;

            return getPages.toString().match(/^-?\d+(?:\.\d{0,0})?/)[0].replace('.','') - 0;
        }
    });
    
    Object.defineProperty(tableFooter.Data, "StartCount",{
        get:function(){
            return ((this.CurrentPage -1)* this.ItemPerPage) + 1;
        }
    });

    //back click
    tableFooter.querySelector('.footer-backbutton').onclick = function(){
        if(tableFooter.Data.CurrentPage > 1)
        {
            tableFooter.Data.CurrentPage--;
        }
    };
    //next page
    tableFooter.querySelector('.footer-nextbutton').onclick = function(){
        if(tableFooter.Data.TotalPage > tableFooter.Data.CurrentPage)
        {
            tableFooter.Data.CurrentPage++;
        }
    };
    return tableFooter.Data;
}

function URLPagingForming(elSource, url, fn)
{
    var el = elSource;

    var itemPerPage = 20;
    var pageNumber = 1;
    var pageNumberEl = el.querySelector('.footer-pageno');
    var itemPerPageEl = el.querySelector('.footer-itemperpage');
    var searchTextEl = el.querySelector('.header-table-searchbox')
    var jsonQuerySearch = {
        SearchText :''
    }
    if(searchTextEl)
        jsonQuerySearch.SearchText = searchTextEl.value;
    if(pageNumberEl)
        pageNumber = pageNumberEl.value;
    if(itemPerPageEl)
        itemPerPage = itemPerPageEl.value;
    

    if(typeof(fn) != 'function')
    {
        console.error('please provide a function that has one parameter');
        throw new Exception('please provide a function that has one parameter');
    }
    fn(jsonQuerySearch);
    
    var stringquerySearch = escape(  JSON.stringify(jsonQuerySearch) );
    return url+pageNumber+'/'+itemPerPage+'/'+stringquerySearch;

}

function  URLPagingForming2(elSource, url, fn){
    
    var el = elSource;

    var itemPerPage = 20;
    var pageNumber = 1;
    var pageNumberEl = el.querySelector('.footer-pageno');
    var itemPerPageEl = el.querySelector('.footer-itemperpage');
    var searchTextEl = el.querySelector('.header-table-searchbox')
    var jsonQuerySearch = {
        SearchText :''
    }
    if(searchTextEl)
        jsonQuerySearch.SearchText = searchTextEl.value;
    if(pageNumberEl)
        pageNumber = pageNumberEl.value;
    if(itemPerPageEl)
        itemPerPage = itemPerPageEl.value;
    

    if(typeof(fn) != 'function')
    {
        console.error('please provide a function that has one parameter');
        throw new Exception('please provide a function that has one parameter');
    }
    fn(jsonQuerySearch);
    //jsonQuerySearch.
    //var stringquerySearch = escape(  JSON.stringify(jsonQuerySearch) );
    //jsonQuerySearch.pageNo
    var compile = {
        pageNo:pageNumber,
        itemCount:itemPerPage,
        jsonSearch: JSON.stringify( jsonQuerySearch )
    }

    return url+"?"+$.param(compile);
}


function PagingResultSync(elSource, DataResult, CallBackFunc){
    var el = elSource;
    var da = DataResult;
    //CLEARING ROWS
    el.querySelectorAll('tbody .cloned').forEach(rowEl=>{
        rowEl.remove();
    });


    el.UpdateData = function( fnFinished = null ){ 
        
        el.querySelector('.footer-pageno').value = 1;
        el.querySelectorAll('tbody .cloned').forEach(rowEl=>{
            rowEl.remove();
        });
        CallBackFunc(el);
        if(fnFinished)
        {
            fnFinished();
        }
    };

    var tabFooter = el.querySelector('.table-footer-container');
    //if(tableFooter)
    //{
        var holder = TableFooterConstraints(tabFooter, function(){ 
            el.querySelectorAll('tbody .cloned').forEach(rowEl=>{
                rowEl.remove();
            });
            CallBackFunc(el);
        });
        if(el.querySelector('.header-table-searchbox'))
            el.querySelector('.header-table-searchbox').onchange = function(){ 
                el.querySelectorAll('tbody .cloned').forEach(rowEl=>{
                    rowEl.remove();
                });
                CallBackFunc(el);
            };
    //}
    if(holder)
    {
        holder.ItemPerPage = da.ItemPerPage;
        holder.CurrentPage = da.Page;
        holder.ItemsFound = da.ItemsFound;
    }
    //el.UpdateData();
    return holder;
}


function SetElementEditMode(el) {

    //FORM VIEW
    var sys_view = el.getAttribute('sys-view');

    if (sys_view == 'formview') {
        // el.querySelectorAll('*[sys-view=formview]').forEach(node => {
        if (!el.getAttribute('data-source')) {
            console.error(el, 'has no data-source');
            return;
        }
        //REMOVING OF EXISTING SETUP
        RemoveElementEditMode(el);

        AppendFormViewEditMode(el);
        //  });
    }

    //Append Table View
    else if (sys_view == 'tableview') {
        // el.querySelectorAll('*[sys-view=tableview]').forEach(node => {
        if (!el.getAttribute('data-source')) {
            console.error(el, 'has no data-source');
            return;
        }
        AppendTableViewEditMode(el);
        //});
    }

    //Append Cell View
    else if (sys_view == 'cellview') {
        //el.querySelectorAll('*[sys-view=cellview]').forEach(node => {
        if (!el.getAttribute('data-source')) {
            console.error(el, 'has no data-source');
            return;
        }
        AppendCellViewEditMode(el);
        //});
    }


}

///FORM VIEW
function AppendFormViewEditMode(el) {

    var headDIV = document.createElement("DIV");
    headDIV.setAttribute('sys-control-buttons', '1');
    headDIV.setAttribute('data-source', el.getAttribute('data-source'));
    headDIV.innerHTML = "  <div style='float:right; display:none;' sys-controls='control'> <button sys-click='FormViewCancel'>CANCEL</button>   </div>  <div style='float:right;' sys-controls='command'> <button sys-click='FormViewAdd'>+</button> <button sys-click='FormViewEdit' >/</button> <button sys-click='FormViewRemove'>Remove</button> </div>";
    headDIV.querySelectorAll('button,div').forEach(function (button) {
        //console.log(button);
        button.setAttribute('data-source', el.getAttribute('data-source'));
    });
    el.prepend(headDIV);
    //el.prepend("Some text", p);

    //console.log(el, "formview");
}
function FormViewAdd(ev) {
    var el = ev.target;

    var datasource = el.getAttribute('data-source');

    toggleFormViewCommandControl(el);
    //CLONING SYSTEM HERE
    //toggleFormViewCommandControl(el);
    var formview = document.querySelector('*[sys-view=formview][data-source=' + datasource + ']');
    if (formview) {
        formview.querySelectorAll('input[data-source=' + datasource + '][data-bind]').forEach(input => {
            //if(input.getAttribute('data-bind'))
            var dataBind = input.getAttribute('data-bind');
            //var canSet = ObjectCanGetSet(window[datasource], dataBind);
            //if(canSet == 2 || canSet == 3)
                input.readOnly = false;
        });

        var add_click = formview.getAttribute('sys-add-click');

        var sysControlView = document.createElement('DIV');
        sysControlView.setAttribute('sys-control-view', '1');
        sysControlView.setAttribute('data-source', datasource);
        sysControlView.innerHTML = '<div style="float:right;"> <button class="add">ADD</button> <button sys-click="FormViewCancel">CANCEL</button> </div> ';
        sysControlView.querySelectorAll('button').forEach(el => {
            el.setAttribute('data-source', datasource);
            if (el.classList.contains('add')) {
                if (add_click) {
                    el.setAttribute('sys-click', add_click);
                }
            }
        });

        formview.append(sysControlView);


        var addload = formview.getAttribute('sys-add-load');
        if (addload) {
            window[addload](ev);
        }

    }
}
function FormViewEdit(ev) {

    var el = ev.target;
    //var formView = el.target.parentElement.parentElement.parentElement;
    var datasource = el.getAttribute('data-source');

    //document.querySelectorAll('')
    toggleFormViewCommandControl(el);

    var formview = document.querySelector('*[sys-view=formview][data-source=' + datasource + ']');

    if (formview) {
        formview.querySelectorAll('input[data-source=' + datasource + '][data-bind]').forEach(input => {
            var dataBind = input.getAttribute('data-bind');
            //var canSet = ObjectCanGetSet(window[datasource], dataBind);
            //if(canSet == 2 || canSet == 3)
                input.readOnly = false;
        });

        //Get Saving 
        var save_click = formview.getAttribute('sys-save-click');

        //BUTTON CONTROLS
        //ADD
        var sysControlView = document.createElement('DIV');
        sysControlView.setAttribute('sys-control-view', '1');
        sysControlView.setAttribute('data-source', datasource);
        sysControlView.innerHTML = '<div style="display:inline-block;"> <button>RESET</button><button class="asnew"> SAVE AS NEW</button> </div><div style="float:right;" >  <button class="save">SAVE</button> <button sys-click="FormViewCancel">CANCEL</button> </div> ';


        sysControlView.querySelectorAll('button').forEach(el => {
            el.setAttribute('data-source', datasource);
            if (el.classList.contains('save')) {
                if (save_click)
                    el.setAttribute('sys-click', save_click);
            }
            else if (el.classList.contains('asnew')) {
                if (save_click)
                    el.setAttribute('sys-click', save_click + '_AsNew');
            }
        });


        formview.append(sysControlView);


        //Loading EDIT
        var editload = formview.getAttribute('sys-edit-load');
        if (editload) {
            window[editload](ev);
        }
    }
}
function FormViewRemove(ev) {
    var el = ev.target;
    var datasource = el.getAttribute('data-source');
    var formview = document.querySelector('*[sys-view=formview][data-source=' + datasource + ']');

    if (formview) {
        //Loading REMOVE
        var removeload = formview.getAttribute('sys-remove-load');
        if (removeload) {
            window[removeload](ev);
        }

    }
}
function FormViewCancel(ev) {

    //RESETTING THE UPDATE THEN
    var el = ev.target;
    var datasource = el.getAttribute('data-source');
    //REMOVING CONTROL
    //REMOVING SYS-CONTROL-VIEW

    var CancelViewClick = document.querySelector('*[data-source=' + datasource + '][sys-view=formview][sys-cancel-click]');
    if (CancelViewClick) {


        if (typeof (window[CancelViewClick.getAttribute('sys-cancel-click')]) == 'function') {

            var cancelclick = window[CancelViewClick.getAttribute('sys-cancel-click')](ev);
            if (cancelclick == false) {

                //console.log("You disable some properties");
                return;
                //return "Hello";
            }
        }
    }

    //RESET VALUES;
    ResetElementBindingSource(ev);

    document.querySelectorAll('*[data-source=' + datasource + '][sys-control-view]').forEach(el => {
        el.remove();
    });
    //TOGGLE TO CANCEL
    //el.parentElement.setAttribute('sys-controls', 'control');
    toggleFormViewCommandControl(el);
}

//
function EmptyRow(tbodyElement, colSpan, cellMessage , overwrite = false)
{
    var row =  tbodyElement.querySelector('.loading-row');
    if(row == null || overwrite)
    {
        var cell = null;
        if(row == null)
        { 
            row = tbodyElement.insertRow(1);
            row.classList.add('loading-row');
            row.setAttribute('align','center');
            cell = row.insertCell(0);
            row.DisplaySet = function( isShow, titleText = null){
            row.style.display = isShow ? '' : 'none';
            if(titleText != null)
                row.querySelector('.loading-title').innerHTML = titleText;
            }
        }
        else
        {
            cell = row.childNodes[0];
        }
        row.style.removeProperty('display');
        cell.innerHTML = '<h3 class="loading-title"> <i class="fa fa-spinner fa-pulse" ></i>'+cellMessage+'</h3>';
        cell.colSpan = colSpan;
    }
    else
    {
        row.style.removeProperty('display');
    }
    return row;
}



//TABLE VIEW
function AppendTableViewEditMode(el) {
    //console.log(el, "tableview");

    if (!el)
        return;
    var headDIV = document.createElement("DIV");
    headDIV.setAttribute('sys-control-buttons', '1');
    headDIV.setAttribute('data-source', el.getAttribute('data-source'));
    var htmlhead = '<div style="float:right; display:none;" sys-controls="control"> <button sys-click="TableViewCancel">CANCEL</button> </div>';
    htmlhead += ' <div style="float: right;" sys-controls="command"> <button class="filter" >Filter</button> <input class="search" /><button class="additem">ADD ITEM</button> <button class="clear">CLEAR</button> </div>';
    headDIV.innerHTML = htmlhead;
    headDIV.querySelectorAll('button, div, input').forEach(function (button) {
        //console.log(button);
        button.setAttribute('data-source', el.getAttribute('data-source'));

        if (button.classList.contains('filter')) {
            var filter =el.getAttribute('sys-filter-click');
            if (filter) {
                button.setAttribute('sys-click', filter);

            }
        }
        else if (button.classList.contains('additem')) {
            var additem = el.getAttribute('sys-additem-click');
            if (additem) {
                button.setAttribute('sys-click', additem);
            }
        }
        else if (button.classList.contains('search')) {
            var search = el.getAttribute('sys-search-change');
            if (search) {
                button.setAttribute('sys-change', search);
            }
        }
        else if (button.classList.contains('clear')) {
            var clear = el.getAttribute('sys-clear-click');
            if (clear) {
                button.setAttribute('sys-click', clear);
            }

        }
    });
    el.prepend(headDIV);
    //console.log('Hello table');

}
function TableViewCancel(ev) {
    //Similar With Form View Concept
    FormViewCancel(ev);
}

//CELL VIEW
function AppendCellViewEditMode(el) {
    //console.log(el, "cellview");
    if (!el) return;
    var headDIV = document.createElement('DIV');
    headDIV.setAttribute('sys-control-buttons', '1');
    headDIV.setAttribute('data-source', el.getAttribute('data-source'));
    var htmlhead = '<div style="float:right; display:none;" sys-controls="control"> <button sys-click="TableViewCancel">CANCEL</button> </div>';
    htmlhead += ' <div style="float: right;" sys-controls="command"><button class="insert">INSERT</button><button class="insertclone">INSERT COPY</button> <button class="remove">X</button> </div>';
    headDIV.innerHTML = htmlhead;
    headDIV.querySelectorAll('button').forEach(function (button) {
        if (button.classList.contains('insertclone')) {
            var insertclone = el.getAttribute('sys-insertclone-click');
            if (insertclone) {
                button.setAttribute('sys-click', insertclone);
            }
        }
        else if (button.classList.contains('remove')) {
            var remove = el.getAttribute('sys-remove-click');
            if (remove) {
                button.setAttribute('sys-click', remove);
            }
        }
    });
    el.prepend(headDIV);
}
function CellViewClone(tableElement, el, afterElement = null) {
    if (!el) return;

    var cloneElement = el.cloneNode(true);
    //GETTING THE DATASOURCE
    var datasource = cloneElement.getAttribute('data-source');
    if (!datasource) return;

    datasource = datasource.split('_')[0];

    cloneElement.setAttribute('data-source', datasource);
    cloneElement.setAttribute('data-source-dynamic', '1');
    cloneElement.classList.add('cellclone');

    if (!afterElement) {
        tableElement.querySelectorAll('.tablebody').forEach(function (tablebody) {
            //console.log(cloneElement);
            console.log(tablebody);
            tablebody.append(cloneElement);
        });
    } else {
        afterElement.insertAdjacentElement("afterend", cloneElement);
    }
    SetDynamicSource(cloneElement);
    cloneElement.style.display = "";
    return cloneElement;
}



function toggleFormViewCommandControl(el) {
    var datasource = el.getAttribute('data-source');

    //REMOVING SYS-CONTROL-VIEW
    document.querySelectorAll('*[data-source=' + datasource + '][sys-control-view]').forEach(el => {
        el.remove();
    });

    //SHOW AND HIDE
    document.querySelectorAll('*[data-source=' + datasource + '][sys-controls]').forEach(function (element) {
        if (element.style.display == 'none')
            element.style.display = '';
        else
            element.style.display = 'none';
            
    });
}
function ElementLoadTrigger(el, func_name) {
    if (typeof (window[func_name]) == 'function')
        setTimeout( 
            function(){ window[func_name](el) }, 10
        );
    else
        console.error(el, ' function ' + func_name + '(el) does not exist!');
}    
function isObject(DataSourceName) {
    if (typeof (window[DataSourceName]) == 'undefined') {
        console.error('The object: (' + DataSourceName + ') does not exist.');
        return false;
    }
    return true;
}

function SystemClickView(el)
{
    var  sysclickview = el.getAttribute('sys-click-view');
    if(sysclickview)
    {
        var sysTarget = el.getAttribute('sys-click-view-target');
        if(sysTarget)
        {
            //With click animation
            if(el.getAttribute('sys-click-view-animate'))
            {
                var next = document.createElement('DIV');
                next.classList.add('backelement-container')
                next.innerHTML =  ViewResourcesData[sysclickview];
                NextAnimateControl( el,document.querySelector(sysTarget), next ,'BACK' );
            }
            else
                document.querySelector(sysTarget).innerHTML =  ViewResourcesData[sysclickview];

            //Call onload function when the view loaded.
            //sys-view-onload
            var onloadview = el.getAttribute('sys-view-onload');
            if(onloadview)
            {
                if(typeof(window[onloadview]) == 'function')
                {
                    window[onloadview](el);
                }
                else
                {
                    console.error(el.target,'did not find the function');
                }
            }
        }
        else
            console.error(el, 'has no target');
    }
}
function SystemViewLoad(el)
{
    var viewName = el.getAttribute('sys-view-load');
    if(viewName)
    {
        var viewElement = document.createElement('DIV');
        viewElement.innerHTML = ViewResourcesData[viewName];
        el.append(viewElement);
        
        var onloadview = el.getAttribute('sys-view-onload');
        if(onloadview)
        {
            if(typeof(window[onloadview]) == 'function')
            {
                window[onloadview](el);
            }
            else
            {
                console.error(el.target,'did not find the function');
            }
        }
    }
}

function SystemClick(ev) {

    //VIEW LOADING
    SystemClickView(ev.target);


    //Submit button
    var isSubmit = false;//SystemSubmitFormClick(ev, fn)
    //JUST ONLY CLICK
    var cl = ev.target.getAttribute('sys-click');
    var clickFn = null;
    //if(e.target.getAttribute('sys-click'))
    if (!cl)
    {   
        cl = ev.target.getAttribute('sys-submit');
        if(!cl)
        {
            return;
        }
        isSubmit = true;
    }
    if(typeof(cl) == 'function')
        clickFn = cl;
    else if(typeof (window[cl]) == 'function')
        clickFn = (window[cl]);


    if (typeof (clickFn) == 'function')
    {
        
       var retval = null;
       if(isSubmit == false)
            retval = clickFn(ev);
        else
        {
            if( typeof(window[ev.target.getAttribute('data-source')]) != 'object')
            {
                console.error('Data source is empty', ev.target);
                return;
            }
            var toSubmitData = {  
                data: JSON.parse( JSON.stringify( window[ev.target.getAttribute('data-source')] ) ), 
                allowSubmit : true 
            };
            clickFn(ev, toSubmitData, false, null  );
            if(toSubmitData.allowSubmit)
            {
                ev.target.setAttribute('sys-preloading', '1');
                if(ev.target.hasAttribute('sys-preloading'))
                {
                    if(!PreLoading(ev))
                    {
                        return;
                    } 
                }
                retval = SystemSubmitFormClick(ev, toSubmitData.data , clickFn);
            }
        }
       if(ev.target.WebRequest)
       {
           //if(ev.target.PreLoading)
           ev.target.WebRequest.PreloadFlags = ev.target.PreloadFlags;
       }
       return retval;
    }
    else
    {
        if(isSubmit)
        {
            if(!cl)
                cl ='FunctionName';
            console.error('Please provide a click function '+cl+'(ev, toSubmitData, isSubmitted, data) for ', ev.target);
        }
            else
            console.error('Please provide a click function(ev) for ', ev.target);
    }
}
function ReloadDataSourceBindingElements(DataSourceName, DisAbling = true) {
    setTimeout(
        function(){
            if (!isObject(DataSourceName)) return;

            if (typeof (window[DataSourceName]) != 'object') return;

            //If array its should return
            if (Array.isArray(window[DataSourceName])) {
                console.log('Its an array.')
                return;
            }
            //var databinds = Object.getOwnPropertyNames(window[DataSourceName]);//Object.keys(window[DataSourceName]);
            //console.log(databinds);
            //for (var i = 0; i < databinds.length; i++) {
            //databind = databinds[i];
            document.querySelectorAll('*[data-source=' + DataSourceName + '][data-bind').forEach(function (node) {
                var bindproperty = '';
                var databind = node.getAttribute('data-bind')
                if(node.type == 'checkbox')
                {   
                    bindproperty = node.getAttribute('data-bind-property') || 'checked';
                    node.disabled = DisAbling;
                } 
                else
                {
                    bindproperty =  node.getAttribute('data-bind-property') || 'value';
                    if (node.tagName == 'INPUT')
                    {
                    if( ObjectCanGetSet(window[DataSourceName], databind) != 1)
                            node.readOnly = DisAbling; 
                    }
                }//node.setAttribute('readonly')//.setAttribute('readonly', ' ');
                //console.log(node.tagName);
                node[bindproperty] = getObjectValue( window[DataSourceName], databind);
                //console.log(typeof(window[DataSourceName][databind]));
            });
        }, 
    20);
    //}
}        
        
        //REUSABLE FUNCTIONS        
function ElementClone(selector, fn = null, elParentElement = null) {
    var parent = null; 
    var clone = null; 
    //console.log(elParentElement);
    if(elParentElement == null)
    {
      parent = document.querySelector(selector).parentElement;
      clone = document.querySelector(selector).cloneNode(true);
    }
    else
    {
        parent = elParentElement;
        clone = elParentElement.querySelector(selector).cloneNode(true);
    }

    clone.style.display = "";
    clone.classList.add('cloned');
    if(fn != null)
        fn(clone);
    parent.appendChild(clone);
    return clone;
}
        
        
        
function SetElementBindingSource(el, newSource, isBindSource = true) {
    var dataSource = el.getAttribute('data-source');
    if (!dataSource) {
        console.error(el, 'has no datasource');
        return;
    }
    if (typeof (window[dataSource]) != 'object') {
        console.error(el, ' is not an object');
        return;
    }

    if (isBindSource) {
        window[dataSource] = newSource;
    }
    else {
        window[dataSource] = JSON.parse(JSON.stringify(newSource));
    }

    window[dataSource + '_old'] = JSON.parse(JSON.stringify(newSource));

    ReloadDataSourceBindingElements(dataSource);

}

function ResetElementBindingSource(ev) {

    var el = ev.target;
    var dataSource = el.getAttribute('data-source');
    if (!dataSource) {
        console.error(el, 'has no datasource');
        return;
    }
    if (typeof (window[dataSource + '_old']) != 'object') {
        console.error(el, 'has no old datasource.');
        return;
    }

    window[dataSource] = JSON.parse(JSON.stringify(window[dataSource + '_old']));

    ReloadDataSourceBindingElements(dataSource);

}

function RemoveElementEditMode(el) {

    document.querySelectorAll('*[sys-control-buttons][data-source=' + el.getAttribute('data-source') + '], *[sys-control-view][data-source=' + el.getAttribute('data-source') + ']').forEach(node => {
        //console.log(node);//.remove();
        node.remove();
    });

}

function  SystemSubmitFormClick(ev, toSubmitData, fn) {

   //if(ev.g)

   var submitMethod= ev.target.getAttribute('sys-submit-method') || 'GET';
   var submitURL = ev.target.getAttribute('sys-submit-url');
   var submitData = toSubmitData;
   if(!submitURL)
   {
        console.error('Please provide a sys-submit-url', ev.target)
       return;
   }
   ev.target.WebRequest =  WebRequest
    (
        submitMethod, 
        submitURL,
        submitData,
        'application/json',
        function(data)
        {
            //console.log(data);
            //if(ev.target.hasAttribute('sys-reloadapplist'))
            var _appListReload = ev.target.getAttribute('sys-reloadapplist');
            if(_appListReload == 1)
            {
                AppListLoad(function(e){
                    //Reloading DataLists
                    //DataListLoad();

                    fn(ev, toSubmitData, true, data);
                    if(ev.target)
                    {
                        ev.target.RespondData = data;
                    }
                    if(ev.target.RequestCompleted)
                    {
                        ev.target.RequestCompleted();
                    }
                });
            }
            else
            {
                fn(ev, toSubmitData, true, data);
                if(ev.target)
                {
                    ev.target.RespondData = data;
                }
                if(ev.target.RequestCompleted)
                {
                    ev.target.RequestCompleted();
                }
            }

        }

    );
    return ev;
}

//ADMIN LTE BUTTONS
function PreLoading(ev){
    //RETURNS FALSE IF STILL PROCESSING

    var el = ev.target;
    var icons = el.querySelector('i');
        if(!icons)
            icons = el;
    var label = el.querySelector('label');
        if(!label)
            label = el;
   
    if( el.PreloadState ){
        if(el.PreloadState == 1)
        {
            el.PreloadFlagsAbort();
        }    
        console.error('Please wait for the process');
        return false;
    }
    
    if(!el){  return;}
    //if(el.tagName != 'BUTTON') return;

    el.PreloadState = 0;
    el.previousInnerHTML = el.innerHTML;
    el.PrevIcon = icons.classList[1];

    var elhtml = el.getAttribute('sys-send-text');
    if(!elhtml)
        elhtml = 'Sending..Please wait.';
    label.innerHTML = elhtml;

    el.classList.add('btn-warning');
    if( icons.classList.contains('fa-check') )
    {
        icons.hasApproveIcon = true;
        icons.classList.remove('fa-check');
    }
    icons.classList.remove(el.PrevIcon);

    icons.classList.add('fa-spinner');
    icons.classList.add('fa-pulse');
    icons.classList.add('no-border');
    
    el.PreloadFlags = function()
    { 
        el.PreloadState++;
        if(el.PreloadState == 2)
        {
            label.innerHTML = 'Loaded-Check';
            if( el.RespondData )
            {
                if(el.RespondData.RetVal == 1)
                {
                    //var result_txt = el.getAttribute('sys-result-text');
                    //if(result_txt)
                    var labelhtml = el.getAttribute('sys-result-text');
                    if(!labelhtml)
                        labelhtml = 'Succeed';
                    label.innerHTML = labelhtml ;
                    el.classList.remove('btn-warning');
                    el.classList.add('btn-success');

                    icons.classList.remove('fa-spinner');
                    icons.classList.remove('fa-pulse');
                    icons.classList.remove('no-border');
                    icons.classList.add('fa-check');
                }
                else
                {
                    var labelhtml =  el.getAttribute('sys-fail-text');
                    if(!labelhtml)
                        labelhtml = 'Failed';
                    label.innerHTML = labelhtml;
                    if(el.getAttribute('sys-fail-text') == 'system')
                    {
                        label.innerHTML = el.RespondData.Message;
                    }
                    el.classList.remove('btn-warning');
                    el.classList.add('btn-danger');
                    
                }
            }
            setTimeout(function()
            {
                el.PreloadFlags();
            }, 1000);
        }
        else if(el.PreloadState == 3)
        {
            el.PreloadState = 0;
            el.innerHTML = el.previousInnerHTML;
            
            icons.classList.remove('fa-spinner');
            icons.classList.remove('fa-pulse');
            icons.classList.remove('no-border');
            //if(!el.hasApproveIcon)
            icons.classList.remove('fa-check');
            el.classList.remove('btn-success');
            el.classList.remove('btn-danger');
            el.classList.remove('btn-warning');
            icons.classList.add(el.PrevIcon);
                
        }
    };
    el.PreloadFlagsAbort = function()
    {
        el.PreloadState = 5;
        label.innerHTML = 'Please Wait..';
        //
        if(el.WebRequest)
            el.WebRequest.abort();
        setTimeout(function()
            {
                el.PreloadState = 5;
                label.innerHTML = 'Please Retry.';
                setTimeout(function()
                {
                    el.PreloadState = 0;
                    el.innerHTML = el.previousInnerHTML;
                    if(icons.classList.contains('fa-spinner'))
                    {
                        icons.classList.remove('fa-spinner');
                        icons.classList.remove('fa-pulse');
                        icons.classList.remove('no-border');

                        icons.classList.remove('fa-check');
                        el.classList.remove('btn-success');
                        el.classList.remove('btn-danger');
                        el.classList.remove('btn-warning');
                        icons.classList.add(el.PrevIcon);
                    }
                }, 1000);
                //el.innerHTML = el.previousInnerHTML;
            }, 2000);
    }
    el.RequestCompleted = function(){
        setTimeout(function()
        {
            el.PreloadFlags();
        }, 1000);
    }
    //RETURNS TRUE IF DONE
    return true;
}
//PRELOADING GITHUB ICONS
function PreLoading1(ev)
{
    //RETURNS FALSE IF STILL PROCESSING

    var el = ev.target;

    if( el.PreloadState ){
        if(el.PreloadState == 1)
        {
            el.PreloadFlagsAbort();
        }    
        console.error('Please wait for the process');
        return false;
    }
    
    if(!el){  return;}
    if(el.tagName != 'BUTTON') return;

    el.PreloadState = 0;
    el.previousInnerHTML = el.innerHTML;
    el.innerHTML = 'Loading';
    if( el.classList.contains('approve') )
    {
        el.hasApproveIcon = true;
        el.classList.remove('approve');
    }
    el.hasIcon = el.classList.contains('icon');
    if(!el.hasIcon)
        el.classList.add('icon');

    el.classList.add('loop');
    el.PreloadFlags = function()
    { 
        el.PreloadState++;
        if(el.PreloadState == 2)
        {
            el.innerHTML = 'Loaded-Check';
            if( el.RespondData )
            {
                if(el.RespondData.RetVal == 1)
                    el.innerHTML = 'Succeed';
                else
                    el.innerHTML =  'Failed';
            }
            setTimeout(function()
            {
                el.PreloadFlags();
            }, 1000);
        }
        else if(el.PreloadState == 3)
        {
            el.PreloadState = 0;
            el.innerHTML = el.previousInnerHTML;
            
            if(el.classList.contains('loop'))
            {
                el.classList.remove('loop');
                if(!el.hasIcon)
                    el.classList.remove('icon');
                if(el.hasApproveIcon)
                    el.classList.add('approve');
                
            }
        }
    };
    el.PreloadFlagsAbort = function()
    {
        el.PreloadState = 5;
        el.innerHTML = 'Aborting';
        //
        if(el.WebRequest)
            el.WebRequest.abort();
        setTimeout(function()
            {
                el.PreloadState = 5;
                el.innerHTML = 'Aborted';
                setTimeout(function()
                {
                    el.PreloadState = 0;
                    el.innerHTML = el.previousInnerHTML;
                    if(el.classList.contains('loop'))
                    {
                        el.classList.remove('loop');
                        el.classList.add('approve');
                    }
                }, 1000);
                //el.innerHTML = el.previousInnerHTML;
            }, 2000);
    }
    el.RequestCompleted = function(){
        setTimeout(function()
        {
            el.PreloadFlags();
        }, 1000);
    }
    //RETURNS TRUE IF DONE
    return true;
}


/**
 * ANIMATIONS
 * NEXT AND BACK
 */

 function NextAnimateControl( eventElement, currentElement, nextElement, backTitle = 'BACK', currentElementTitle = '', buttonClasses = ['ghbutton','danger','arrowleft', 'icon' , 'big'] )
 {
    wrapcontent = currentElement;
    if(wrapcontent)
    {
        wrapcontent.parentElement.classList.add('nextpageanimation-container')
    }

    if(wrapcontent == null)
    {
        wrapcontent = eventElement.closest('.nextpageanimation-container');
        if(wrapcontent)
            wrapcontent.querySelector('.backelement-container');
        else
        {
            console.error('It is recommend [sys-click-view-target] - a selector to target element if its not in the first slide. other else please [add sys-click-view-target=notarget] to the next slide.');
            return;
        }
    }

    var width = wrapcontent.offsetWidth + 500;

    //UNDER TESTING FOR NEXT 
    wrapcontent.BackCount = wrapcontent.parentElement.querySelectorAll('.backelement-container').length + 1;
    nextElement.BackCount = wrapcontent.BackCount;
    //REPLACEMENT wrapcontent for sliding.
    if(wrapcontent.BackCount > 1)
    {
        var _backCounter = 1;
            wrapcontent.parentElement.querySelectorAll('.backelement-container').forEach(backEl=>{
            if(wrapcontent.BackCount - 1 == _backCounter )
                wrapcontent = backEl;
            _backCounter++;
        })
    }
    ///UNDER TESING UNTIL HERE
    
    wrapcontent.style.opacity = 0;
    var leftout = wrapcontent.animate(
        { 
            transform:[ 'translateX(0px)','translateX(-500px)','translateX(-'+width+'px)' ] ,
            easing: ['ease-out' , 'ease-in'],
            opacity: [0.9, 0],
            position : ['absolute','absolute']
        },
        500
    );
    leftout.onfinish = function()
    {
        wrapcontent.style.display = 'none';
    }

    //SLIDE LEFT IN
    //BACK BUTTON
    var button = document.createElement('button');
    button.BackElement  = wrapcontent;
    if(buttonClasses)
    {
        for( var i = 0; i < buttonClasses.length; i++)
            button.classList.add(buttonClasses[i]);
    }
    button.innerHTML = backTitle+currentElementTitle;
    button.onclick = BackAnimateControl;
    nextElement.prepend(button);

    
    //BACKING
    wrapcontent.parentElement.append(nextElement);
    nextElement.animate(
            { 
                transform: ['translateX(580px)','translateX(0px)'] , 
                easing: ['ease-out' , 'ease-in'], 
                opacity : [0, 0.2, 1],
                position : ['absolute','absolute']
            }, 1000
    ).onfinish = function()
    {
        //nextElement.style.removeProperty('position');
    }

 }

 function BackAnimateControl(evt)
 {
    var currentElement = evt.target.parentElement;
    var previousElement = evt.target.BackElement;
    var backCount = currentElement.BackCount;
    currentElement.animate(
        { 
            transform: ['translateX(0px)','translateX(580px)'] ,  
            easing: ['ease-out'], 
            opacity : [0.8, 0.2, 0],
            position: ['absolute','absolute']
        }, 500
    ).onfinish = function(){
        currentElement.remove();
    };

    previousElement.style.removeProperty('display');
    var xwidth = previousElement.offsetWidth ;
    previousElement.style.opacity = 1;
    previousElement.animate(
        { 
            transform: ['translateX(-'+xwidth+'px)', 'translateX(0px)'] ,
            width: [xwidth+'px', xwidth+'px' ],
            position: ['absolute','absolute'],
            opacity: [0,0.9 ]
        },
        500
    ).onfinish = function()
    {
        //if(backCount == 1)
            //previousElement.BackCount = 0;
    }

 }
 function AppendAnimateControl( parentElement, childElement, backTitle = 'REMOVE', currentElementTitle = '', buttonClasses = ['ghbutton','danger','remove', 'icon'] )
 {
    //BACK BUTTON
    var button = document.createElement('button');
    button.cancelElement = childElement;
    button.onclick  = AppendAnimateCancel;
    if(buttonClasses)
    {
        for( var i = 0; i < buttonClasses.length; i++)
            button.classList.add(buttonClasses[i]);
    }
    button.innerHTML = backTitle+currentElementTitle;
    childElement.append(button);

    parentElement.append(childElement);
    childElement.animate(
        { 
            transform: ['translateX(580px)'] , 
            easing: ['ease-out' , 'ease-in'], 
            opacity : [0, 0.2, 1],
            position : ['absolute','absolute']
        }, 1000
    );
 }
 
 function AppendAnimateCancel(evt)
 {
     var cancelEl = evt.target.cancelElement;
     cancelEl.animate(
        { 
            transform: ['translateX(580px)' ],  
            easing: ['ease-out'], 
            opacity : [0.8, 0.2, 0],
            position: ['absolute','absolute']
        }, 500
    ).onfinish = function(){
        cancelEl.remove();
    };

 }

 function searchSelectElement(event) {
    //var  ul, li;
    var input = event.target; //document.querySelector(".searchselect-input");
    var filter = input.value.toUpperCase();

    var _keyInput = event.key.toUpperCase();
    //console.log(event.key);

    if(!input.MaxSelection)
        input.MaxSelection = 0;
    //ARROW KEYS HERE
    if(!input.ArrowSelected || input.ArrowSelected < 0)
      input.ArrowSelected = 0;
    var _arrowUpDown = false;
    if(_keyInput == 'ARROWDOWN')
    {
      input.ArrowSelected++;
      //console.log(input.ArrowSelected, input.MaxSelection);
      if(input.ArrowSelected > input.MaxSelection)
        input.ArrowSelected = 1;
      _arrowUpDown = true
    }
    else if(_keyInput == 'ARROWUP')
    {
      input.ArrowSelected--;
      if(input.ArrowSelected <= 0)
        input.ArrowSelected = input.MaxSelection;
      _arrowUpDown = true;
    }


  var target_searched_container = input.closest('.searchselectdropdown').querySelector('.searchselect-container');

  if(_arrowUpDown || _keyInput == 'ENTER')
    {
      //CLEAR UP THE CURRENT HIGHLIGHTS

      //HIGHLIGHTING SELECT HERE..
      //console.log(input.ArrowSelected);
      var _indexCounter = 1;
      target_searched_container.querySelectorAll('.cloned').forEach(element => {
        if(_indexCounter == input.ArrowSelected)
        {
          element.classList.add('searchselect-selected');
          if(_keyInput == 'ENTER')
          {
            //console.log(element);
            element.click();
          }
        }
        else
          element.classList.remove('searchselect-selected');
        _indexCounter++;
      });
      if(_arrowUpDown)
        return;
    }


    //console.log();
    target_searched_container.querySelectorAll('.cloned').forEach(element => {
      element.remove();
    });
    if(_keyInput == 'ESCAPE' || _keyInput == 'ENTER')
    {
      return;
    }


    //SET INPUT SELECTION
    input.ArrowSelected = 0;


    //TESTING A GETTING THE LIST
    if( typeof(getObjectValue) == undefined)
    {
      console.error('You need Xpose.js')
      return;
    }
    var _list = input.getAttribute('sys-selecttype-list');
    _list = getObjectValue(window, _list);
    if(!_list || _list.length <= 0)
    {
      console.error('Empty List');
      return;
    }
    //console.log(_list);
    
    //CONSTRAINT ITEM
    var valid_constraint = false;
    var _constraint = input.getAttribute('sys-selecttype-item-constraint');
    var _itemdisplay = input.getAttribute('sys-selecttype-display');


    if(typeof( window[_constraint]) == 'object' )
    {
      valid_constraint = true;
    }
    //GET THE BIND VALUE
    var _displaylength = 0;

    //
    filter = filter.split(' ');
    //console.log(filter);
    //return;

    for(var i = 0; i < _list.length && _displaylength < 10; i++)
    {
      var item = _list[i];

      //SEARCH ELEMENT IF GOT CONTAINS
      var _hasNotmatch = false;
      for(var j= 0 ; j < filter.length; j++)
      {
        if(JSON.stringify(item).toUpperCase().indexOf(filter[j]) < 0)
          {
            _hasNotmatch = true;
            break;
          }
      }
      if(_hasNotmatch) continue;
      _displaylength++;


      //DISPLAY ITEM HERE.
      if(valid_constraint)
        item = window.Defaults.ObjectMerge(_constraint, _list[i]);
      

      var cloned = ElementClone('.searchselect-cloneable', null, target_searched_container);
      cloned.style.removeProperty('display');
      
      var display = getObjectValue(item, _itemdisplay);
      cloned.innerHTML = display;
      cloned.Data = item;
      cloned.onclick = function(ev)
      {

        if(!ev.target.classList.contains('searchselect-cloneable'))
          return;
        if(!ev.target.Data)
        {
          console.error(ev.target,'No Data Found');
          return;
        }

        //INPUT DISPLAY
        var _inputdisplay = input.getAttribute('sys-selecttype-input-display');
        var _itemid = input.getAttribute('sys-selecttype-ID');

        //CALL BACK ITEM
        var _callbackFunction = input.getAttribute('sys-selecttype-callback');


        if(!_inputdisplay || _inputdisplay.trim() == '')
        {
          console.error( ev.target, 'Please provide [sys-selecttype-input-display]');
          return;
        }
        if(!_itemid || _itemid == '')
        {
          console.error( ev.target, 'Please provide  [sys-selecttype-ID]');
          return;
        }

        //PROCEED HERE..
        var input_value = getObjectValue(ev.target.Data, _inputdisplay);
        if(!input_value || input_value.trim() =='') 
        {
          console.error( ev.target, 'Your display is empty [sys-selecttype-input-display]');
          return;
        }
        input.value = input_value;

        input.setAttribute('data-id', getObjectValue(ev.target.Data, _itemid));
        input.Data = ev.target.Data;
        //console.log(ev.target.Data);
        //CLEAR AFTER SELECTION
        target_searched_container.querySelectorAll('.cloned').forEach(element => {
        element.remove();
        });
        if( typeof(window[_callbackFunction]) != 'function' || window[_callbackFunction] == searchSelectElement)
        {
            console.error( 'Please provide call back function for functionCall(el) at attribute sys-selecttype-callback', input);
            return;
        }
        window[_callbackFunction](input);

      }


      //console.log(display);
    }
    input.MaxSelection = _displaylength;
}

///COOKIES
 function setCookie(cname, cvalue, exdays = 30) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    //SameSite=None; Secure;
    document.cookie = cname + "=" + cvalue + ";expires=" + expires +";path=/";
  }
  
  function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }
        
//OBJECT DEFAULTS AND CLONING
window.XDefaults = {
    Add: function ( identifier, obj){

        window.Defaults['_'+identifier] = this.Clone(obj);
        Object.defineProperty(window.Defaults,identifier, {
            get:function(){
                return this.Clone(window.Defaults['_'+identifier]);
            }
        });
    },
    Clone:function(source) {
            //SHALLOW COPY
            var target = {};
          let descriptors = Object.getOwnPropertyNames(source).reduce((descriptors, key) => {

            if( typeof( source[key] ) == 'object')
            {
                //RECURSIVE COPYING FROM A AN OBJECT
                target[key] = this.Clone(source[key]);
            }
            else
                descriptors[key] = Object.getOwnPropertyDescriptor(source, key);
            return descriptors;
          }, {});
          
          // By default, Object.assign copies enumerable Symbols, too
          Object.getOwnPropertySymbols(source).forEach(sym => {
            //let descriptor = Object.getOwnPropertyDescriptor(source, sym);
            if (descriptor.enumerable) {
                descriptors[sym] = descriptor;
            }
          });

            Object.defineProperties(target, descriptors);
        //});
        
        return target;
    },
    ObjectMerge: function(identifier, objValue, overwrite = true)
    {
        var objmerge = this.Clone( window.Defaults['_'+identifier] );
        //console.log(objmerge);
        this.Merging(objmerge, objValue, overwrite);
        return  objmerge;
    },
    Merging: function(Target, Source, overwrite = true)
    {
        Object.keys(Source).forEach(key=>{
        if(typeof(Target[key]) == 'object')
        {
            if( typeof(Source[key]) == 'object' )
                this.Merging(Target[key], Source[key]);
        }
        else if(overwrite)
            Target[key] = Source[key];
        else if(Target[key] != undefined)
            Target[key] = Source[key];
             
         });
    }
};


//FILE UPLOAD
function UploadFile(el)
{
    let photo = el.files[0];  // file from input
    if(!el.getAttribute('data-source'))
    {
        console.error('please provide data-source to reload for', el);
        return;
    }

    let req = new XMLHttpRequest();
    let formData = new FormData();
    req.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
             var uploadVal = JSON.parse(this.responseText);
             //if(uploadVal.RetVal == 1)
                //fileValueEl.value = uploadVal.DataID;

            
            if(uploadVal.RetVal == 0)
            {
                console.error(uploadVal.Message);
                return;
            }
            else
            {
                console.log(uploadVal);
                //FileID
                setObjectValue(window[ el.getAttribute('data-source') ], el.getAttribute('sys-data-bind-target-id'), uploadVal.DataID);
                var getExtInfo = JSON.parse(uploadVal.Message);
                
                //FileExt
                setObjectValue(window[ el.getAttribute('data-source') ], el.getAttribute('sys-data-bind-target-ext'), getExtInfo.FileExt);

                //window[ el.getAttribute('data-source') ].ImageID = uploadVal.DataID;
            }
            
            //setObjectValue(window[fileValueEl.getAttribute('data-source')],fileValueEl.getAttribute('data-bind'), uploadVal.DataID );
        }
    }

    formData.append("fileToUpload", photo);                                
    req.open("POST", 'content/fileupload.php');
    req.send(formData);
}


/**
 * REUSABLE FUNCTION TO CREATE HOVER ELEMENT
 */
window.XposecreateHoverDiv = function(el, fnLoad)
{
    //GETTING THE the data-hover and test if the div already exists.
    if(window[el])
        return window[el];
    console.log('Hover has been activated');
    
    //Create the DIV Container that would wrap the current element
    var container = document.createElement('DIV');
    container.style.display = "inline-block";
    window[el] = container;

    //WRAP THE Element 
    el.parentElement.insertBefore(container, el);
    
    //TRANSFER THE EL to the container
    container.append(el);

    //Create a hover container
    var hoverDivElement = document.createElement('DIV');
    hoverDivElement.classList.add('hoverelement-hidden-container');
    hoverDivElement.innerHTML = "";
    hoverDivElement.style.zIndex = 10000; 
    container.append(hoverDivElement);
    if(typeof(fnLoad) == 'function')
    {
        fnLoad(container);
    }
    return container;
}

//BOOTSTRAP ACTIVATING MENU
function ActivateMenu(active_links)
{
  if(Array.isArray(active_links))
  {
    active_links.forEach((item, index)=>{
        var item = document.querySelector('i.'+item);
        if(item)
        {
            var link = item.closest('a.nav-link');
            if(link)
            {
                link.classList.add('active');
            }
        }
    });
  }
  else
  {
      var item = document.querySelector('i.'+active_links);
      var link = item.closest('a.nav-link');
      if(link)
      {
        link.classList.add('active');
      }
  }
}



///XPOSE DATE HELPERS
Date.prototype.addDays = function(days) {
    this.setDate(this.getDate() + parseInt(days));
    return this;
};

Date.prototype.dateYMD = function(){
        var d = this;
        var  month = '' + (d.getMonth() + 1);
        var  day = '' + d.getDate();
        var year = d.getFullYear();

      if (month.length < 2) 
          month = '0' + month;
      if (day.length < 2) 
          day = '0' + day;

      return [year, month, day].join('-');
}

Date.prototype.dateTitle = function(){
      return (this.getMonth() + 1)+'/'+ this.getDate()+'/'+ this.getUTCFullYear().toString().substring(2)

}
Date.prototype.getWeekOfMonth = function() {
  var firstWeekday = new Date(this.getFullYear(), this.getMonth(), 1).getDay();
  var offsetDate = this.getDate() + firstWeekday - 1;
  return Math.floor(offsetDate / 7);
}
Date.prototype.getMonthName = function(){
    var m = this.getMonth();
    var abb_year = this.getUTCFullYear().toString().substring(2);
    if(m == 0) return 'Jan'+abb_year;
    if(m == 1) return 'Feb'+abb_year;
    if(m == 2) return 'Mar'+abb_year;
    if(m == 3) return 'Apr'+abb_year;
    if(m == 4) return 'May'+abb_year;
    if(m == 5) return 'Jun'+abb_year;
    if(m == 6) return 'Jul'+abb_year;
    if(m == 7) return 'Aug'+abb_year;
    if(m == 8) return 'Sep'+abb_year;
    if(m == 9) return 'Oct'+abb_year;
    if(m == 10) return 'Nov'+abb_year;
    if(m == 11) return 'Dec'+abb_year;
}
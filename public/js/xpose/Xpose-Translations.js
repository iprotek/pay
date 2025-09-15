window.xposeLang = {
    editButton:null,
    selectedElement:null,
    translationContainer:null,
    textToTranslate:"",
     
    getLabelElementList:function(){
       return  document.querySelectorAll('p[label-trans-id],label[label-trans-id],th[label-trans-id],span[label-trans-id],h1[label-trans-id],h2[label-trans-id],h3[label-trans-id],h4[label-trans-id]'); 
    },
    
     showEditButton:function(){
        this.createTranslationPopUp();
        this.editButton = document.createElement('BUTTON');
        this.editButton.innerHTML = "<span class='fa fa-edit'></span>";
        this.editButton.style.zIndex = 9999;
        this.editButton.style.display = 'none';
        this.editButton.style.position = 'absolute';
        //this.editButton.setAttribute('data-toggle', 'modal');
        //this.editButton.setAttribute('data-target','#translation-container-modal');
        this.editButton.onmouseover = (evt)=>{ evt.target.style.display = ''; };//this.mouseOverShowButton;
        this.editButton.onmouseleave = (evt)=>{ evt.target.style.display = 'none'; };
        this.editButton.onclick = this.showDialog;
        document.querySelector('body').prepend(this.editButton);
        this.editButton.title = 'Set Language';
        this.editButton.setAttribute("class",  "btn btn-success btn-xs");//.class = "btn btn-success btn-xs m-0 p-0";
        //console.log(this.editButton);
        this.editButton.translationContainer = this.translationContainer;
        this.getLabelElementList().forEach((item, index)=>{

            if(item.getAttribute('label-trans-id'))
            {
                item.style.cursor = 'pointer';
                item.style.color = 'blue';
                //item.title = 'Set Language';// item.getAttribute('label-trans-id');
                //item.onclick = this.showDialog;
                item.onmouseover = this.mouseOverShowButton;
                item.onmouseleave = this.mouseLeaveHideButton;

                //Important in rebind this
                item.buttonShow = this.buttonShow;
                item.editButton = this.editButton; // important in reference this as it is bind to mouseover, or any events.
                item.selectedElement = item;
                item.buttonHide = this.buttonHide;
                //item.translationContainer = this.translationContainer;
            }
            //console.log(item);
        });

    },
    createTranslationPopUp(){
        this.translationContainer = document.createElement('DIV');
        document.querySelector('body').prepend(this.translationContainer);
        this.translationContainer.id = "translation-container-modal";
        this.translationContainer.innerHTML = '';

        var dialog = document.createElement('DIV');
        this.translationContainer.append(dialog);
        dialog.setAttribute("class", "modal-dialog");


        var modal_content = document.createElement('DIV');
        dialog.append(modal_content);
        modal_content.setAttribute("class","modal-content");


        var modal_header = document.createElement('DIV');
        modal_content.append(modal_header);
        modal_header.setAttribute('class', 'modal-header');
        modal_header.innerHTML = '<h4 class="modal-title">SET TRANSLATION TEXT</h4><button type="button" class="close" data-dismiss="modal">&times;</button>';

        var modal_body = document.createElement('DIV');
        modal_content.append(modal_body);
        modal_body.setAttribute("class", "modal-body");
        
        modal_body.innerHTML = 
        //'<div class="input-group"><span class="input-group-text rounded-0 border-0"><i class="fa fa-search"></i></span> <input type="text" placeholder="Search English Word." class="m-0 form-control rounded-0 input-sm"></div>'
        //+'<p><i>Search in English only.</i></p><br/>'
        //+
        '<div> Translating: <b class="text-to-translate">n/A</b></div>'
        +'<div class="translation-languages card p-2 mt-2">'
        //+ this.createTranslateInput(null, 'English', 'en')
        //+ this.createTranslateInput(null, 'Chinese', 'zh')
        //+ this.createTranslateInput(null, 'Vietnamese', 'vi')
        //+ this.createTranslateInput(null, 'Combodian', 'km')
        +'</div>';

        var modal_footer = document.createElement('DIV');
        modal_content.append(modal_footer);
        modal_footer.setAttribute("class","modal-footer");
        modal_footer.innerHTML = '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> <button type="button" class="btn btn-primary float-left" > <label class="m-0" label-trans-id="save_translation" onclick="SaveLanguageTranslation(event)"> SAVE TRANSLATION </label> </button>';

        

        this.translationContainer.setAttribute('class', "modal fade");
        this.translationContainer.setAttribute('role', "dialog"); 

    },
    showDialog:function (evt){
        //console.log(evt);
        //document.querySelector('#translation-container-modal').showModal();
        $('#translation-container-modal').modal({
            backdrop: 'static',
            keyboard: false
        }, 'show');
        var current = this;
        this.translationContainer.LabelTransID = this.LabelTransID;
        this.translationContainer.selectedElement = this.selectedElement;
        
        this.translationContainer.querySelector('.text-to-translate').innerHTML = this.textToTranslate;
        var fnTranslation = function(_cur){
            
            var translations = JSON.parse( _cur.selectedElement.Grammar.translations );
            var result = "";
            translations.forEach((translation, index)=>{
                
            var selected_translation = translation.Translations.filter(a=>a.Selected == 1)[0];
            var val = "";
            if(selected_translation)
                val = selected_translation.Words;
            else if(_cur.selectedElement.Grammar.label_trans_id == 'default' && translation.Code == 'en')
                val = _cur.textToTranslate;
                
            console.log(val, translation.Translations);
            result +=  _cur.createTranslateInput(null, translation.Name, translation.Code, val);
            })
            _cur.translationContainer.querySelector('.translation-languages').innerHTML = result;

        }

        //SET the translation container
        //IF SELECTED ELEMENT HAS GRAMMAR
        if(this.selectedElement.Grammar)
        {
            fnTranslation(current);
        }
        else{
            WebRequest2('POST','/languages',  JSON.stringify(  {'label_trans_ids': [this.LabelTransID] } ), 'application/json').then(result=>{
                if(result.ok)
                    return result.json();
            }).then(data=>{
                if(data.grammars.length > 0){
                    current.selectedElement.Grammar = data.grammars[0];
                }
                else
                    current.selectedElement.Grammar = data.default;
                fnTranslation(current);
            });
        }
        //console.log(result);


        //SET THE INPUT
        
        //SET ALSO THE data-list

    },
    createTranslateInput:function(info, lang_description, lang_abbr, value=""){
        return '<div class="input-group mt-1"><span class="input-group-text rounded-0 border-0" ><i >'+lang_description+'('+lang_abbr+')</i></span> <input type="text" lang-abbr="'+lang_abbr+'" value="'+value+'" placeholder="'+lang_description+'" class="language-input-text m-0 form-control rounded-0 input-sm"></div>';
    },
    mouseOverShowButton:function(evt){
        //this.selectedElement = evt.target;    
        this.editButton.LabelTransID = this.selectedElement.getAttribute('label-trans-id');
        this.editButton.textToTranslate = this.selectedElement.innerHTML;
        this.editButton.selectedElement = this.selectedElement;
        this.editButton.createTranslateInput = window.xposeLang.createTranslateInput;

        this.buttonShow();
    },
    mouseLeaveHideButton:function(evt){
        //this.selectedElement = evt.target;
        this.buttonHide();
        //console.log("Hide");
    },
    buttonShow:function(){
       this.editButton.style.display = '';
        //
       var bodyRect = document.body.getBoundingClientRect();
       var elemRect = this.getBoundingClientRect();
       var offset   = elemRect.top - bodyRect.top -15;
       var offsetLeft = (elemRect.left - bodyRect.left) -15;
       this.editButton.style.marginTop = offset+'px';
       this.editButton.style.marginLeft = offsetLeft+'px';
       this.style.textDecoration = 'underline';

       //console.log(offsetLeft);
    },
    buttonHide:function(){
        this.editButton.style.display = 'none';
        this.style.textDecoration = 'none';
    },
    setUILanguage( el, lang){
        var grammar = el.Grammar;
        var translations = JSON.parse( grammar.translations );
        if(translations){
            var _translation = translations.filter(a=>a.Code == lang)[0];
            if(_translation){
                if(_translation.Translations && _translation.Translations.length > 0)
                {
                    var selected_translation = _translation.Translations.filter(a=>a.Selected == 1)[0];
                    if(!selected_translation)
                        selected_translation = _translation.Translations[0];
                        el.innerHTML = "";
                        el.append( selected_translation.Words )
                }
            }
        }
    }


}
window.SaveLanguageTranslation = function(evt){
    //console.log(xposeLang.translationContainer);
    var label_trans_id = xposeLang.translationContainer.LabelTransID;
    
    var request_obj = {};
    xposeLang.translationContainer.querySelectorAll('.language-input-text[lang-abbr]').forEach((el,index)=>{
        request_obj[el.getAttribute('lang-abbr')] = el.value;
    });
    var request = { label_trans_id:label_trans_id, grammars:request_obj };

    WebRequest2('POST', '/admin/languages/'+label_trans_id, request, 'application/json' ).then(result=>{
        if(result.ok)
            return result.json();
    }).then(data=>{
        //console.log(data);
        var selectedElement =  xposeLang.translationContainer.selectedElement;
        selectedElement.Grammar = data.grammar;
        xposeLang.setUILanguage(selectedElement, data.lang);       
        $('#translation-container-modal').modal('hide'); 
    })

}


setTimeout(function(){

    //Check if the user is allow to edit language
    //GET ALL label_trans_ids    
    var label_trans_ids = [];    
    window.xposeLang.getLabelElementList().forEach((item, index)=>{
        label_trans_ids.push(item.getAttribute('label-trans-id'));
    });
    WebRequest2('POST','/languages', JSON.stringify( {'label_trans_ids':label_trans_ids}), 'application/json').then(result=>{
        if(result.ok)
            return result.json();
    }).then(
        res=>{        
        var lang = res.lang;
        window.xposeLang.getLabelElementList().forEach((item, index)=>{

            item.Grammar = res.default;
            var grammar = res.grammars.filter(a=>{
                return a.label_trans_id.toLowerCase().trim() == item.getAttribute('label-trans-id').toLowerCase().trim();
            })[0];

            if(grammar)
            {
                //SET GRAMMAR OBJECT
                item.Grammar = grammar;
                //SET LABEL DISPLAY LANGUAGE
                xposeLang.setUILanguage(item, lang);
                /*
                var translations = JSON.parse( grammar.translations );
                if(translations){
                    var _translation = translations.filter(a=>a.Code == lang)[0];
                    if(_translation){
                        if(_translation.Translations && _translation.Translations.length > 0)
                        {
                            var selected_translation = _translation.Translations.filter(a=>a.Selected == 1)[0];
                            if(!selected_translation)
                                selected_translation = _translation.Translations[0];
                            item.innerHTML = "";
                            item.append( selected_translation.Words )
                        }
                    }
                }
                */
            }
        });

        //IF EDIT MODE
        if(res.mode == 'edit')
            window.xposeLang.showEditButton();
    
    });

    WebRequest2('GET', '/languages', null, 'application/json').then(result=>{
        if(result.ok)
            return result.json();
    }).then(data=>{
        //languagelist
        console.log(data);
        document.querySelectorAll('select.languagelist').forEach((selectEl, index)=>{
            
            data.list.forEach((item, itemIndex)=>{
                var option = document.createElement('OPTION');
                option.value = item.code;
                option.innerHTML = item.name;
                selectEl.append(option);
            })
            selectEl.value = data.lang;
            selectEl.onchange = function(evt){
                WebRequest2('POST', '/languages/set/'+evt.target.value, null, 'application/json').then(result=>{
                    window.location.reload();
                });
            };
        });
    });


}, 200);
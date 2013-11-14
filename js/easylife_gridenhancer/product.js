/**
 * Easylife_GridEnhancer extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE_GRID_ENHANCER.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Easylife
 * @package        Easylife_GridEnhancer
 * @copyright      Copyright (c) 2013
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */

if(typeof Easylife_GridEnhancer=='undefined') {
    var Easylife_GridEnhancer = {};
}
Easylife_GridEnhancer.Product = Class.create();
Easylife_GridEnhancer.Product.prototype = {
    initialize: function(element, config){
        var that = this;
        this.templateSyntax = /(^|.|\r|\n)({{(\w+)}})/;
        this.element = $(element);
        this.system = config.system_attributes;
        this.positionIdentifier = config.position_identifier;
        this.fieldIdentifier = config.field_identifier;
        this.deleteIdentifier = config.delete_identifier;
        this.fieldTemplate = $(config.field_template).innerHTML;
        this.mainField = $(config.main_field);
        this.allOptions = config.all_options;
        this.values = config.current;
        this.fields = [];
        this.fieldIndex = 0;
        this.noticed = false;
        this.noticeAt = config.notice_at;
        this.noticeMessage = config.notice_message;
        this.initFields();
        this.reloadPositions();
        $$(config.add_field_identifier).each(function(elem){
            Event.observe($(elem), 'click', function(){
                that.addField(false);
            })
        })
        this.setFieldsJson()
    },
    setFieldsJson: function(){
        this.mainField.value = this.getFieldsJson(true);
    },
    getFieldsJson: function(asString){
        var object = {};
        for (var i = 0;i<this.fields.length;i++){
            var key = this.fields[i].getCurrentValue();
            var position = this.fields[i].getCurrentAfter();
            object[key] = position;
        }
        if (asString){
            return JSON.stringify(object);
        }
        return object;
    },

    reloadPositions: function(){
        var positionOptions = this.getPositionOptions();
        for (var i = 0;i<this.fields.length;i++){
            var currentPosition = this.fields[i].getCurrentAfter();
            this.fields[i].positionElement.options.length = 0;
            this.fields[i].positionElement.options.add(new Option('', ''));
            for (var j in positionOptions){
                if (positionOptions.hasOwnProperty(j)){
                    if (j != this.fields[i].getCurrentValue()){
                        this.fields[i].positionElement.options.add(new Option(positionOptions[j], j, (j == currentPosition) ? 'selected="selected' : ''));
                    }
                }
            }
            //IE wants it only this way.
            this.fields[i].positionElement.value = currentPosition;
        }
    },
    getPositionOptions: function(){
        var options = JSON.parse(JSON.stringify(this.system));
        for (var i in this.fields){
            if (this.fields.hasOwnProperty(i)){
                var value = this.fields[i].getCurrentValue();
                var label = this.fields[i].getCurrentLabel();
                options[value] = label;
            }
        }
        return options;
    },
    initFields: function(){
        for (var i in this.values){
            if (this.values.hasOwnProperty(i)){
                var obj = {
                    field: i,
                    position:this.values[i]
                };
                this.addField(obj)
            }
        }
        this.reloadFields();
    },
    addField:function(obj){
        var template = new Template(this.fieldTemplate, this.templateSyntax);
        var fieldTemplate = template.evaluate({id:this.fieldIndex, id1:this.fieldIndex + 1});
        $(this.element.select('table tbody')[0]).insert(fieldTemplate);
        var field = new Easylife_GridEnhancer.ProductField(
            $('field_container1_' + this.fieldIndex),
            this,
            {
                fieldIdentifier: this.fieldIdentifier,
                positionIdentifier: this.positionIdentifier,
                deleteIdentifier: this.deleteIdentifier,
                index: this.fieldIndex
            },
            obj
        );
        this.fields.push(field);
        this.fieldIndex++;
        if (obj == false){
            this.reloadFields();
            if (this.fields.length >= this.noticeAt && this.noticed == false){
                this.noticed = true;
                alert(this.noticeMessage);
            }
        }
    },
    reloadFields: function(){
        var setOptions = this.getFieldsJson(false);
        for (var i = 0; i<this.fields.length;i++){
            this.fields[i].reloadOptions(setOptions);
        }
    },
    removeField: function(index){
        for (var i = 0; i<this.fields.length; i++) {
            if (this.fields[i].index == index) {
                this.fields.splice(i, 1);
                this.reloadFields();
                this.reloadPositions();
                break;
            }
        }

    }
}
Easylife_GridEnhancer.ProductField = Class.create();
Easylife_GridEnhancer.ProductField.prototype = {
    initialize: function(container, grid, config, selected){
        var that = this;
        this.grid = grid;
        this.container = $(container);
        this.config = config;
        this.index = config.index;
        this.element = $(this.container).select(this.config.fieldIdentifier)[0];
        this.positionElement = $(this.container).select(this.config.positionIdentifier)[0];
        this.deleteButton = $(this.container).select(this.config.deleteIdentifier)[0];
        Event.observe($(this.element), 'change', function(){
            that.reload();
            that.grid.setFieldsJson();
        });
        Event.observe($(this.positionElement), 'change', function(){
            that.position = that.positionElement.value;
            that.grid.setFieldsJson();
        });
        Event.observe($(this.deleteButton), 'click', function(){
            that._delete();
        });
        if (typeof selected == "object"){
            this.element.value = selected.field;
            this.position = selected.position;
        }

    },
    reload: function(){
        this.grid.reloadPositions();
        this.grid.reloadFields();
    },
    getCurrentValue: function(){
        return this.element.value;
    },
    getCurrentLabel: function(){
        var index = this.element.selectedIndex;
        return (index > 0) ? this.element.options[index].innerHTML : '';
    },
    _delete: function(){
        this.grid.removeField(this.index);
        this.container.remove();
    },
    getCurrentAfter: function(){
        return this.position;
    },
    reloadOptions: function(exclude){
        var allOptions = this.grid.allOptions;
        var currentVal = this.getCurrentValue();
        this.element.options.length = 0;
        for (var i in allOptions){
            if (allOptions.hasOwnProperty(i)){
                if (typeof exclude[allOptions[i].value] == "undefined" || allOptions[i].value == currentVal){
                    var option = new Option(allOptions[i].label, allOptions[i].value, (allOptions[i].value == currentVal) ? 'selected="selected' : '')
                    this.element.options.add(option);
                }
            }
        }
        //IE fix. it seams IE does not take into account "selected" when building a new Option element.
        //Damn you IE (*me waving left fist in the air*)
        this.element.value = currentVal;
    }
}

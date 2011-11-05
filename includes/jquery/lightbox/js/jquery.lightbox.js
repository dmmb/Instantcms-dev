/**
 * This file is part of jQuery Lightbox
 * Copyright (C) 2007-2010 Benjamin Arthur Lupton
 * http://www.balupton.com/projects/jquery-lightbox
 *
 * jQuery Lightbox is free software; You can redistribute it and/or modify it under the terms of
 * the GNU Affero General Public License version 3 as published by the Free Software Foundation.
 * You don't have to do anything special to accept the license and you don�t have to notify
 * anyone which that you have made that decision.
 * 
 * jQuery Lightbox is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See your chosen license for more details.
 * 
 * You should have received along with jQuery Lightbox:
 * - A copy of the license used.
 *   If not, see <http://www.gnu.org/licenses/agpl-3.0.html>.
 * - A copy of our interpretation of the license used.
 *   If not, see <http://github.com/balupton/jquery-lightbox/blob/master/COPYING.txt>.
 * 
 * @version 1.4.6-final
 * @date July 28, 2010
 * @since v0.1.0-dev, December 3, 2007
 * @category jquery-plugin
 * @package jquery-lightbox {@link http://www.balupton/projects/jquery-lightbox}
 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
 * @copyright (c) 2007-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
 * @example Visit {@link http://www.balupton.com/projects/jquery-lightbox} for more information.
 */

// Start of our jQuery Plugin
(function($)
{	// Create our Plugin function, with $ as the argument (we pass the jQuery object over later)
	// More info: http://docs.jquery.com/Plugins/Authoring#Custom_Alias
	
	/**
	 * Console Emulator
	 * We have to convert arguments into arrays, and do this explicitly as webkit (chrome) hates function references, and arguments cannot be passed as is
	 * @version 1.0.2
	 * @date August 21, 2010
	 * @since 0.1.0-dev, December 01, 2009
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	if ( typeof window.console !== 'object' || typeof window.console.emulated === 'undefined' ) {
		// Check to see if console exists
		if ( typeof window.console !== 'object' || !(typeof window.console.log === 'function' || typeof window.console.log === 'object') ) {
			// Console does not exist
			window.console = {};
			window.console.log = window.console.debug = window.console.warn = window.console.trace = function(){};
			window.console.error = function(){
				var msg = "An error has occured. More information will be available in the console log.";
				for ( var i = 0; i < arguments.length; ++i ) {
					if ( typeof arguments[i] !== 'string' ) { break; }
					msg += "\n"+arguments[i];
				}
				if ( typeof Error !== 'undefined' ) {
					throw new Error(msg);
				}
				else {
					throw(msg);
				}
			};
		}
		else {
			// Console is object, and log does exist
			// Check Debug
			if ( typeof window.console.debug === 'undefined' ) {
				window.console.debug = function(){
					var arr = ['console.debug:']; for(var i = 0; i < arguments.length; i++) { arr.push(arguments[i]); };
				    window.console.log.apply(window.console, arr);
				};
			}
			// Check Warn
			if ( typeof window.console.warn === 'undefined' ) {
				window.console.warn = function(){
					var arr = ['console.warn:']; for(var i = 0; i < arguments.length; i++) { arr.push(arguments[i]); };
				    window.console.log.apply(window.console, arr);
				};
			} 
			// Check Error
			if ( typeof window.console.error === 'undefined' ) {
				window.console.error = function(){
					var arr = ['console.error']; for(var i = 0; i < arguments.length; i++) { arr.push(arguments[i]); };
				    window.console.log.apply(window.console, arr);
				};
			}
			// Check Trace
			if ( typeof window.console.trace === 'undefined' ) {
				window.console.trace = function(){
				    window.console.error.apply(window.console, ['console.trace does not exist']);
				};
			}
		}
		// We have been emulated
		window.console.emulated = true;
	}
	
	/**
	 * Return a new JSON object of the old string.
	 * Turns:
	 * 		file.js?a=1&amp;b.c=3.0&b.d=four&a_false_value=false&a_null_value=null
	 * Into:
	 * 		{"a":1,"b":{"c":3,"d":"four"},"a_false_value":false,"a_null_value":null}
	 * @version 1.1.0
	 * @date July 16, 2010
	 * @since 1.0.0, June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	String.prototype.queryStringToJSON = String.prototype.queryStringToJSON || function ( )
	{	// Turns a params string or url into an array of params
		// Prepare
		var params = String(this);
		// Remove url if need be
		params = params.substring(params.indexOf('?')+1);
		// params = params.substring(params.indexOf('#')+1);
		// Change + to %20, the %20 is fixed up later with the decode
		params = params.replace(/\+/g, '%20');
		// Do we have JSON string
		if ( params.substring(0,1) === '{' && params.substring(params.length-1) === '}' )
		{	// We have a JSON string
			return eval(decodeURIComponent(params));
		}
		// We have a params string
		params = params.split(/\&(amp\;)?/);
		var json = {};
		// We have params
		for ( var i = 0, n = params.length; i < n; ++i )
		{
			// Adjust
			var param = params[i] || null;
			if ( param === null ) { continue; }
			param = param.split('=');
			if ( param === null ) { continue; }
			// ^ We now have "var=blah" into ["var","blah"]
		
			// Get
			var key = param[0] || null;
			if ( key === null ) { continue; }
			if ( typeof param[1] === 'undefined' ) { continue; }
			var value = param[1];
			// ^ We now have the parts
		
			// Fix
			key = decodeURIComponent(key);
			value = decodeURIComponent(value);
			try {
			    // value can be converted
			    value = eval(value);
			} catch ( e ) {
			    // value is a normal string
			}
		
			// Set
			// window.console.log({'key':key,'value':value}, split);
			var keys = key.split('.');
			if ( keys.length === 1 )
			{	// Simple
				json[key] = value;
			}
			else
			{	// Advanced (Recreating an object)
				var path = '',
					cmd = '';
				// Ensure Path Exists
				$.each(keys,function(ii,key){
					path += '["'+key.replace(/"/g,'\\"')+'"]';
					jsonCLOSUREGLOBAL = json; // we have made this a global as closure compiler struggles with evals
					cmd = 'if ( typeof jsonCLOSUREGLOBAL'+path+' === "undefined" ) jsonCLOSUREGLOBAL'+path+' = {}';
					eval(cmd);
					json = jsonCLOSUREGLOBAL;
					delete jsonCLOSUREGLOBAL;
				});
				// Apply Value
				jsonCLOSUREGLOBAL = json; // we have made this a global as closure compiler struggles with evals
				valueCLOSUREGLOBAL = value; // we have made this a global as closure compiler struggles with evals
				cmd = 'jsonCLOSUREGLOBAL'+path+' = valueCLOSUREGLOBAL';
				eval(cmd);
				json = jsonCLOSUREGLOBAL;
				delete jsonCLOSUREGLOBAL;
				delete valueCLOSUREGLOBAL;
			}
			// ^ We now have the parts added to your JSON object
		}
		return json;
	};

	/**
	 * Remove a element, or a set of elements from an array
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @copyright John Resig
	 * @license MIT License - {@link http://opensource.org/licenses/mit-license.php}
	 */
	Array.prototype.remove = function(from, to) {
		var rest = this.slice((to || from) + 1 || this.length);
		this.length = from < 0 ? this.length + from : from;
		return this.push.apply(this, rest);
	};

	/**
	 * Get a element from an array at [index]
	 * if [current] is set, then set this index as the current index (we don't care if it doesn't exist)
	 * @version 1.0.1
	 * @date July 09, 2010
	 * @since 1.0.0 June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.get = function(index, current) {
		// Determine
		if ( index === 'first' ) {
			index = 0;
		} else if ( index === 'last' ) {
			index = this.length-1;
		} else if ( index === 'prev' ) {
			index = this.index-1;
		} else if ( index === 'next' ) {
			index = this.index+1;
		} else if ( !index && index !== 0 ) {
			index = this.index;
		}
	
		// Set current?
		if ( current||false !== false ) {
			this.setIndex(index);
		}
	
		// Return
		return this.exists(index) ? this[index] : undefined;
	};

	/**
	 * Apply the function [fn] to each element in the array
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.each = function(fn){
		for (var i = 0; i < this.length; ++i) {
			if (fn(i, this[i], this) === false) 
				break;
		}
		return this;
	}

	/**
	 * Checks whether the index is a valid index
	 * @version 1.0.0
	 * @date July 09, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.validIndex = function(index){
		return index >= 0 && index < this.length;
	};

	/**
	 * Set the current index of the array
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.setIndex = function(index){
		if ( this.validIndex(index) ) {
			this.index = index;
		} else {
			this.index = null;
		}
		return this;
	};

	/**
	 * Get the current index of the array
	 * If [index] is passed then set that as the current, and return it's value
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.current = function(index){
		return this.get(index, true);
	};

	/**
	 * Get whether or not the array is empty
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.isEmpty = function(){
		return this.length === 0;
	};

	/**
	 * Get whether or not the array has only one item
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.isSingle = function(){
		return this.length === 1;
	};

	/**
	 * Get whether or not the array is not empty
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.isNotEmpty = function(){
		return this.length !== 0;
	};

	/**
	 * Get whether or not the array has more than one item
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.isNotEmpty = function(){
		return this.length > 1;
	};

	/**
	 * Get whether or not the current index is the last one
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.isLast = function(index){
		index = typeof index === 'undefined' ? this.index : index;
		return !this.isEmpty() && index === this.length-1;
	}

	/**
	 * Get whether or not the current index is the first one
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.isFirst = function(index){
		index = typeof index === 'undefined' ? this.index : index;
		return !this.isEmpty() && index === 0;
	}

	/**
	 * Clear the array
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.clear = function(){
		this.length = 0;
	};

	/**
	 * Set the index as the next one, and get the item
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.next = function(update){
		return this.get(this.index+1, update);
	};

	/**
	 * Set the index as the previous one, and get the item
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.prev = function(update){
		return this.get(this.index-1, update);
	};

	/**
	 * Reset the index
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.reset = function(){
		this.index = null;
		return this;
	};

	/**
	 * Set the [index] to the [item]
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.set = function(index, item){
		// We want to set the item
		if ( index < this.length && index >= 0 ) {
			this[index] = item;
		} else {
			throw new Error('Array.prototype.set: [index] above this.length');
			// return false;
		}
		return this;
	};

	/**
	 * Set the index as the next item, and return it.
	 * If we reach the end, then start back at the beginning.
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.loop = function(){
		if ( !this.index && this.index !== 0 ) {
			// index is not a valid value
			return this.current(0);
		}
		return this.next();
	};

	/**
	 * Add the [arguments] to the array
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.add = function(){
		this.push.apply(this,arguments);
		return this;
	};

	/**
	 * Insert the [item] at the [index] or at the end of the array
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.insert = function(index, item){
		if ( typeof index !== 'number' ) {
			index = this.length;
		}
		index = index<=this.length ? index : this.length;
		var rest = this.slice(index);
		this.length = index;
		this.push(item);
		this.push.apply(this, rest);
		return this;
	};

	/**
	 * Get whether or not the index exists in the array
	 * @version 1.0.0
	 * @date July 09, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.exists = Array.prototype.exists || function(index){
		return typeof this[index] !== 'undefined';
	};

	/**
	 * Get whether or not the value exists in the array
	 * @version 1.0.0
	 * @date June 30, 2010
	 * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	Array.prototype.has = Array.prototype.has || function(value){
		var has = false;
		for ( var i=0, n=this.length; i<n; ++i ) {
			if ( value == this[i] ) {
				has = true;
				break;
			}
		}
		return has;
	};

	/**
	 * Bind a event only once
	 * @version 1.0.0
	 * @date June 30, 2010
     * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	$.fn.once = $.fn.once || function(event, data, callback){
		// Only apply a event handler once
		var $this = $(this);
		// Handle
		if ( (callback||false) ) {
			$this.unbind(event, callback);
			$this.bind(event, data, callback);
		} else {
			callback = data;
			$this.unbind(event, callback);
			$this.bind(event, callback);
		}
		// Chain
		return $this;
	};
	
	/**
	 * Bind a event, with or without data
	 * Benefit over $.bind, is that $.binder(event, callback, false|{}|''|false) works.
	 * @version 1.0.0
	 * @date June 30, 2010
     * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	$.fn.binder = $.fn.binder || function(event, data, callback){
		// Help us bind events properly
		var $this = $(this);
		// Handle
		if ( (callback||false) ) {
			$this.bind(event, data, callback);
		} else {
			callback = data;
			$this.bind(event, callback);
		}
		// Chain
		return $this;
	};
	
	/**
	 * Event for the last click for a series of one or more clicks
	 * @version 1.0.0
	 * @date July 16, 2010
     * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	$.fn.lastclick = $.fn.lastclick || function(data,callback){
		return $(this).binder('lastclick',data,callback);
	};
	$.event.special.lastclick = $.event.special.lastclick || {
		setup: function( data, namespaces ) {
			$(this).bind('click', $.event.special.lastclick.handler);
		},
		teardown: function( namespaces ) {
			$(this).unbind('click', $.event.special.lastclick.handler);
		},
		handler: function( event ) {
			// Setup
			var clear = function(){
				// Fetch
				var Me = this;
				var $el = $(Me);
				// Fetch Timeout
				var timeout = $el.data('lastclick-timeout')||false;
				// Clear Timeout
				if ( timeout ) {
					clearTimeout(timeout);
				}
				timeout = false;
				// Store Timeout
				$el.data('lastclick-timeout',timeout);
			};
			var check = function(event){
				// Fetch
				var Me = this;
				clear.call(Me);
				var $el = $(Me);
				// Store the amount of times we have been clicked
				$el.data('lastclick-clicks', ($el.data('lastclick-clicks')||0)+1);
				// Handle Timeout for when All Clicks are Completed
				var timeout = setTimeout(function(){
					// Fetch Clicks Count
					var clicks = $el.data('lastclick-clicks');
					// Clear Timeout
					clear.apply(Me,[event]);
					// Reset Click Count
					$el.data('lastclick-clicks',0);
					// Fire Event
					event.type = 'lastclick';
					$.event.handle.apply(Me, [event,clicks])
				},300);
				// Store Timeout
				$el.data('lastclick-timeout',timeout);
			};
			// Fire
			check.apply(this,[event]);
		}
	};
	
	/**
	 * Prevent the default action when a click is performed
	 * @version 1.0.0
	 * @date August 19, 2010
	 * @since 1.0.0, August 19, 2010
     * @package jquery-sparkle {@link http://www.balupton/projects/jquery-sparkle}
	 * @author Benjamin "balupton" Lupton {@link http://www.balupton.com}
	 * @copyright (c) 2009-2010 Benjamin Arthur Lupton {@link http://www.balupton.com}
	 * @license GNU Affero General Public License version 3 {@link http://www.gnu.org/licenses/agpl-3.0.html}
	 */
	$.fn.preventDefault = $.fn.preventDefault || function(){
		return $(this).click(function(event){
			event.preventDefault();
			return false;
		});
	};
	

	// Declare our class
	$.LightboxClass = function ( )
	{	// This is the handler for our constructor
		this.construct();
	};
	
	// Extend jQuery elements for Lightbox
	$.fn.lightbox = function ( options )
	{	// Init a el for Lightbox
		// Eg. $('#gallery a').lightbox();
		
		// If need be: Instantiate $.LightboxClass to $.Lightbox
		$.Lightbox = $.Lightbox || new $.LightboxClass();
		
		// Handle IE6
		if ( $.Lightbox.ie6 && !$.Lightbox.ie6_support )
		{	// We are IE6 and we want to ignore
			return this; // chain
		}
		
		// Establish options
		options = $.extend({start:false,events:true} /* default options */, options);
		
		// Get group
		var $group = $(this);
		
		// Events?
		if ( options.events )
		{	// Add events
			$group.preventDefault().once('lastclick',function(event){
				// Get obj
				var $obj = $(this);
				// Get position
				var index = $group.index($obj);
				// Init group
				if ( !$.Lightbox.init(index, $group) )
				{	return false;	}
				// Display lightbox
				if ( !$.Lightbox.start() )
				{	return false;	}
				// Cancel href
				event.preventDefault();
				return false;
			});
			// Add style
			$group.addClass('lightbox-enabled');
		}
		
		// Start?
		if ( options.start )
		{	// Start
			// Get obj
			var obj = $(this);
			// Get rel
			// var rel = $(obj).attr('rel');
			// Init group
			if ( !$.Lightbox.init(0, $group) )
			{	return this;	}
			// Display lightbox
			if ( !$.Lightbox.start() )
			{	return this;	}
		}
		
		// And chain
		return this;
	};
	
	// Define our class
	$.extend($.LightboxClass.prototype,
	{	// Our LightboxClass definition
		
		// -----------------
		// Everything to do with images
		
		images: [],
		
		// -----------------
		// Variables
		
		constructed:		false,
		compressed:			null,
		
		// -----------------
		// Options
		
		src:				null,		// the source location of our js file
		baseurl:			null,
		
		files: {
			compressed: {
				scripts: {
					lightbox:	'/includes/jquery/lightbox/js/jquery.lightbox.min.js',
					colorBlend:	'/includes/jquery/lightbox/js/jquery.color.min.js'
				},
				styles: {
					lightbox:	'/includes/jquery/lightbox/css/jquery.lightbox.min.css'
				}
			},
			uncompressed: {
				scripts: {
					lightbox:	'/includes/jquery/lightbox/js/jquery.lightbox.js',
					colorBlend:	'/includes/jquery/lightbox/js/jquery.color.js'
				},
				styles: {
					lightbox:	'/includes/jquery/lightbox/css/jquery.lightbox.css'
				}
			},
			images: {
				prev:		'/includes/jquery/lightbox/images/prev.gif',
				next:		'/includes/jquery/lightbox/images/next.gif',
				blank:		'/includes/jquery/lightbox/images/blank.gif',
				loading:	'/includes/jquery/lightbox/images/loading.gif'
			}
		},
		
		text: {
			// For translating
			image:		'�����������',
			of:			'��',
			close:		'������� X',
			closeInfo:	'������� �� ����� ����� ��� ����������� ����� �������.',
			download:	'�������.',
			help: {
				close:		'�������, ����� �������',
				interact:	'Hover to interact'
			},
			about: {
				text: 	'jQuery Lightbox Plugin (balupton edition)',
				title:	'Licenced under the GNU Affero General Public License.',
				link:	'http://www.balupton.com/projects/jquery-lightbox'
			}
		},
		
		keys: {
			close:	'c',
			prev:	'p',
			next:	'n'
		},
		
		handlers: {
			// For custom actions
			show:	null
		},
		
		opacity:		0.9,
		padding:		null,		// if null - autodetect
		
		speed:			200,		// Duration of effect, milliseconds
		
		rel:			'lightbox',	// What to look for in the rels
		
		auto_relify:	true,		// should we automaticly do the rels?
		
		auto_scroll:	'follow',	// should the lightbox scroll with the page? follow, disabled, ignore
		auto_resize:	true,		// true or false
		
		ie6:			null,		// are we ie6?
		ie6_support:	true,		// have ie6 support
		
		colorBlend:		null,		// null - auto-detect, true - force, false - no
		
		download_link:		true,	// Display the download link
		
		show_helper_text:	false,	// Display the helper text up the top right
		show_linkback:		false,	// true, false
		show_info:			'auto',	// auto - automaticly handle, true - force
		show_extended_info:	'auto',	// auto - automaticly handle, true - force	
		
		// names of the options that can be modified
		options:	['show_helper_text', 'auto_scroll', 'auto_resize', 'download_link', 'show_info', 'show_extended_info', 'ie6_support', 'colorBlend', 'baseurl', 'files', 'text', 'show_linkback', 'keys', 'opacity', 'padding', 'speed', 'rel', 'auto_relify'],
		
		// -----------------
		// Functions
		
		construct: function ( options )
		{	// Construct our Lightbox
			
			// -------------------
			// Prepare
			
			// Initial construct
			var initial = typeof this.constructed === 'undefined' || this.constructed === false;
			this.constructed = true;
			
			// Perform domReady
			var domReady = initial;
			
			// Prepare options
			options = options || {};
			
			// -------------------
			// Handle files
			
			// Prepend function to use later
			var prepend = function(item, value) {
				if ( typeof item === 'object' ) {
					for (var i in item) {
						item[i] = prepend(item[i], value);
					}
				} else if ( typeof value === 'array' ) {
					for (var i=0,n=item.length; i<n; ++i) {
						item[i] = prepend(item[i], value);
					}
				} else {
					item = value+item;
				}
				return item;
			}
			
			// Add baseurl
			if ( initial && (typeof options.files === 'undefined') )
			{	// Load the files like default
				
				// Reset compressed
				this.compressed = null;
				
				// Get the src of the first script tag that includes our js file (with or without an appendix)
				var $script = $('script[src*='+this.files.compressed.scripts.lightbox+']:first');
				if ( $script.length !== 0 ) {
					// Compressed
					$.extend(true, this.files, this.files.compressed);
					this.compressed = true;
				} else {
					// Uncompressed
					$script = $('script[src*='+this.files.uncompressed.scripts.lightbox+']:first');
					if ( $script.length !== 0 ) {
						// Uncompressed
						$.extend(true, this.files, this.files.uncompressed);
						this.compressed = false;
					} else {
						// Nothing
					}
				}
				
				// Make sure we found ourselves
				if ( this.compressed === null )
				{	// We didn't
					window.console.error('Lightbox was not able to find it\'s javascript script tag necessary for auto-inclusion.');
					// We don't work with files anymore, so don't care for domReady
					domReady = false;
				}
				else
				{	// We found ourself
					
					// Grab the script src
					this.src = $script.attr('src');
					
					// The baseurl is the src up until the start of our js file
					this.baseurl = this.src.substring(0, this.src.indexOf(this.files.scripts.lightbox));
					
					// Prepend baseurl to files
					this.files = prepend(this.files, this.baseurl);
					
					// Now as we have source, we may have more params
					options = $.extend(options, this.src.queryStringToJSON());
				}
				
				// Create
				this.images.image = {
					src:	'',
					title:	'��� ��������',
					description:	'',
					name:	'',
					color:	null,
					width:	null,
					height:	null,
					id: 	null,
					image:	true
				};
				this.images.prepare = function(obj){
					var image = $.extend({}, this.image);
					if ( obj.tagName ) {
						// We are an element
						obj = $(obj);
						if ( obj.attr('src') || obj.attr('href') ) {
							image.src = obj.attr('src') || obj.attr('href');
							image.title = obj.attr('title') || obj.attr('alt') || image.title;
							image.name = obj.attr('name') || '';
							image.color = obj.css('backgroundColor');
							// Extract description from title
							var s = image.title.indexOf(': ');
							if ( s > 0 )
							{	// Description exists
								image.description = image.title.substring(s+2) || image.description;
								image.title = image.title.substring(0,s) || image.title;
							}
						} else {
							image = null;
						}
					} else if ( obj.src ) {
						// We are a image object
						image = $.extend(this.image,obj);
					} else {
						image = null;
					}
					if ( image ) {
						image.id = image.id || image.src+image.title+image.description;
					}
					return image;
				}
				this.images.create = function(obj){
					var images = this;
					if ( obj.each ) {
						obj.each(function(index,item){
							images.create(item);
						});
						return;
					}
					
					var image = images.prepare(obj);
					
					if ( !image ) {
						window.console.error('We dont know what we have:', obj, image);
					} else {
						images.push(image);
					}
					
					return images;
				};
			}
			else
			if ( typeof options.files === 'object' )
			{	// We have custom files
				// Prepend baseurl to files
				options.files = prepend(options.files, this.baseurl);
			}
			else
			{	// Don't have any files, so no need to perform domReady
				domReady = false;
			}
			
			// -------------------
			// Apply options
			
			for ( var i in this.options )
			{	// Cycle through the options
				var name = this.options[i];
				if ( (typeof options[name] === 'object') && (typeof this[name] === 'object') )
				{	// We have a group like text or files
					this[name] = $.extend(true, this[name], options[name]);
				}
				else if ( typeof options[name] !== 'undefined' )
				{	// We have that option, so apply it
					this[name] = options[name];
				}
			}	delete i;
			
			// -------------------
			// Figure out what to do
			
			// Handle IE6
			if ( initial && navigator.userAgent.indexOf('MSIE 6') >= 0 )
			{	// Is IE6
				this.ie6 = true;
			}
			else
			{	// We are not IE6
				this.ie6 = false;
			}
			
			// -------------------
			// Handle our DOM
			
			if ( domReady || typeof options.download_link !== 'undefined' ||  typeof options.colorBlend !== 'undefined' || typeof options.files === 'object' || typeof options.text === 'object' || typeof options.show_linkback !== 'undefined' || typeof options.scroll_with !== 'undefined' )
			{	// We have reason to handle the dom
				$(function() {
					// DOM is ready, so fire our DOM handler
					$.Lightbox.domReady();
				});
			}
			
			// -------------------
			// Finish Up
			
			// All good
			return true;
		},
		
		domReady: function ( )
		{
			// -------------------
			// Include resources
			
			// Grab resources
			var bodyEl = document.getElementsByTagName($.browser.safari ? 'head' : 'body')[0];
			var stylesheets = this.files.styles;
			var scripts = this.files.scripts;
			
			// colorBlend
			if ( this.colorBlend === true && typeof $.colorBlend === 'undefined' )
			{	// Force colorBlend
				this.colorBlend = true;
				// Leave file in place to be loaded
			}
			else
			{	// We either have colorBlend or we don't
				this.colorBlend = typeof $.colorBlend !== 'undefined';
				// Remove colorBlend file
				delete scripts.colorBlend;
			}
			
			// Include stylesheets
			for ( stylesheet in stylesheets )
			{
				var linkEl = document.createElement('link');
				linkEl.type = 'text/css';
				linkEl.rel = 'stylesheet';
				linkEl.media = 'screen';
				linkEl.href = stylesheets[stylesheet];
				linkEl.id = 'lightbox-stylesheet-'+stylesheet.replace(/[^a-zA-Z0-9]/g, '');
				$('#'+linkEl.id).remove();
				bodyEl.appendChild(linkEl);
			}
			
			// Include javascripts
			delete scripts.lightbox; // prevent us from including ourself
			for ( script in scripts )
			{
				var scriptEl = document.createElement('script');
				scriptEl.type = 'text/javascript';
				scriptEl.src = scripts[script];
				scriptEl.id = 'lightbox-script-'+script.replace(/[^a-zA-Z0-9]/g, '');
				$('#'+scriptEl.id).remove();
				bodyEl.appendChild(scriptEl);
			}
			
			// Cleanup
			delete scripts;
			delete stylesheets;
			delete bodyEl;
			
			// -------------------
			// Append display
			
			// Append markup
			$('#lightbox,#lightbox-overlay').remove();
			$('body').append('<div id="lightbox-overlay"><div id="lightbox-overlay-text">'+(this.show_linkback?'<p><span id="lightbox-overlay-text-about"><a href="#" title="'+this.text.about.title+'">'+this.text.about.text+'</a></span></p><p>&nbsp;</p>':'')+(this.show_helper_text?'<p><span id="lightbox-overlay-text-close">'+this.text.help.close+'</span><br/>&nbsp;<span id="lightbox-overlay-text-interact">'+this.text.help.interact+'</span></p>':'')+'</div></div><div id="lightbox"><div id="lightbox-imageBox"><div id="lightbox-imageContainer"><img id="lightbox-image" /><div id="lightbox-nav"><a href="#" id="lightbox-nav-btnPrev"></a><a href="#" id="lightbox-nav-btnNext"></a></div><div id="lightbox-loading"><a href="#" id="lightbox-loading-link"><img src="' + this.files.images.loading + '" /></a></div></div></div><div id="lightbox-infoBox"><div id="lightbox-infoContainer"><div id="lightbox-infoHeader"><span id="lightbox-caption">'+(this.download_link ? '<a href="#" title="' + this.text.download + '" id="lightbox-caption-title"></a>' : '<span id="lightbox-caption-title"></span>')+'<span id="lightbox-caption-seperator"></span><span id="lightbox-caption-description"></span></span></div><div id="lightbox-infoFooter"><span id="lightbox-currentNumber"></span><span id="lightbox-close"><a href="#" id="lightbox-close-button" title="'+this.text.closeInfo+'">' + this.text.close + '</a></span></div><div id="lightbox-infoContainer-clear"></div></div></div></div>');
			
			// Update Boxes - for some crazy reason this has to be before the hide in safari and konqueror
			this.resizeBoxes();
			this.repositionBoxes();
			
			// Hide
			$('#lightbox,#lightbox-overlay,#lightbox-overlay-text-interact').hide();
			
			// -------------------
			// Browser specifics
			
			// Handle IE6
			if ( this.ie6 && this.ie6_support )
			{	// Support IE6
				// IE6 does not support fixed positioning so absolute it
				// ^ This is okay as we disable scrolling
				$('#lightbox-overlay').css({
					position:	'absolute',
					top:		'0px',
					left:		'0px'
				});
			}
			
			// -------------------
			// Preload Images
			
			// Cycle and preload
			$.each(this.files.images, function()
			{	// Proload the image
				var preloader = new Image();
				preloader.onload = function() {
					preloader.onload = null;
					preloader = null;
				};	preloader.src = this;
			});
			
			// -------------------
			// Apply events
			
			// If the window resizes, act appropriatly
			$(window).unbind('resize').resize(function ()
			{	// The window has been resized
				$.Lightbox.resizeBoxes('resized');
			});
			
			// If the window scrolls, act appropriatly
			if ( this.scroll === 'follow' )
			{	// We want to
				$(window).scroll(function ()
				{	// The window has scrolled
					$.Lightbox.repositionBoxes();
				});
			}
			
			// Prev
			$('#lightbox-nav-btnPrev').unbind().preventDefault().hover(function() { // over
				$(this).css({ 'background' : 'url(' + $.Lightbox.files.images.prev + ') left 45% no-repeat' });
			},function() { // out
				$(this).css({ 'background' : 'transparent url(' + $.Lightbox.files.images.blank + ') no-repeat' });
			}).lastclick(function() {
				$.Lightbox.showImage('prev');
				return false;
			});
					
			// Next
			$('#lightbox-nav-btnNext').unbind().preventDefault().hover(function() { // over
				$(this).css({ 'background' : 'url(' + $.Lightbox.files.images.next + ') right 45% no-repeat' });
			},function() { // out
				$(this).css({ 'background' : 'transparent url(' + $.Lightbox.files.images.blank + ') no-repeat' });
			}).lastclick(function() {
				$.Lightbox.showImage('next');
				return false;
			});
			
			// Help
			if ( this.show_linkback )
			{	// Linkback exists so add handler
				$('#lightbox-overlay-text-about a').preventDefault().lastclick(function(){window.open($.Lightbox.text.about.link); return false;});
			}
			$('#lightbox-overlay-text-close').unbind().hover(
				function(){
					$('#lightbox-overlay-text-interact').fadeIn();
				},
				function(){
					$('#lightbox-overlay-text-interact').fadeOut();
				}
			);
			
			// Image link
			if ( this.download_link ) {
				$('#lightbox-caption-title').preventDefault().lastclick(function(){window.open($(this).attr('href')); return false;});
			}
			
			// Assign close clicks
			$('#lightbox-overlay, #lightbox, #lightbox-loading-link, #lightbox-btnClose').unbind().preventDefault().lastclick(function() {
				$.Lightbox.finish();
				return false;	
			});
			
			// -------------------
			// Finish Up
			
			// Relify
			if ( this.auto_relify )
			{	// We want to relify, no the user
				this.relify();
			}
			
			// All good
			return true;
		},
		
		relify: function ( )
		{	// Create event
		
			//
			var groups = {};
			var groups_n = 0;
			var orig_rel = this.rel;
			// Create the groups
			$.each($('[rel*='+orig_rel+']'), function(index, obj){
				// Get the group
				var rel = $(obj).attr('rel');
				// Are we really a group
				if ( rel === orig_rel )
				{	// We aren't
					rel = groups_n; // we are individual
				}
				// Does the group exist
				if ( typeof groups[rel] === 'undefined' )
				{	// Make the group
					groups[rel] = [];
					groups_n++;
				}
				// Append the image
				groups[rel].push(obj);
			});
			// Lightbox groups
			$.each(groups, function(index, group){
				$(group).lightbox();
			});
			// Done
			return true;
		},
		
		init: function ( image, images )
		{	// Init a batch of lightboxes
			
			// Establish images
			if ( typeof images === 'undefined' ) {
				images = image;
				image = 0;
			}
			
			// Clear
			this.images.clear();
				
			// Add images
			this.images.create(images);
			
			// Do we need to bother
			if ( this.images.isEmpty() ) {
				// No images
				window.console.warn('WARNING', 'Lightbox started, but no images: ', image, images);
				return false;
			}
			
			// Set current
			if ( !this.images.current(image) ) {
				// No images
				window.console.warn('WARNING', 'Could not find current image: ', image, this.images);
				return false;
			}
			
			// Done
			return true;
		},
		
		start: function ( )
		{	// Display the lightbox
				
			// We are alive
			this.visible = true;
			
			// Adjust scrolling
			if ( this.scroll === 'disable' )
			{	// 
				$(document.body).css('overflow', 'hidden');
			}
			
			// Fix attention seekers
			$('embed, object, select').css('visibility', 'hidden');//.hide(); - don't use this, give it a go, find out why!
			
			// Resize the boxes appropriatly
			this.resizeBoxes('general');
			
			// Reposition the Boxes
			this.repositionBoxes({'speed':0});
			
			// Hide things
			$('#lightbox-infoFooter').hide(); // we hide this here because it makes the display smoother
			$('#lightbox-image,#lightbox-nav,#lightbox-nav-btnPrev,#lightbox-nav-btnNext,#lightbox-infoBox').hide();
					
			// Display the boxes
			$('#lightbox-overlay').css('opacity',this.opacity).fadeIn(400, function(){
				// Show the lightbox
				$('#lightbox').fadeIn(300);
				
				// Display first image
				if ( !$.Lightbox.showImage() ) {
					$.Lightbox.finish();
					return false;
				}
			});
			
			// All done
			return true;
		},
		
		finish: function ( )
		{	// Get rid of lightbox
		
			// Hide lightbox
			$('#lightbox').hide();
			$('#lightbox-overlay').fadeOut(function() { $('#lightbox-overlay').hide(); });
			
			// Fix attention seekers
			$('embed, object, select').css({ 'visibility' : 'visible' });//.show();
			
			// Kill current image
			this.images.reset();
			
			// Adjust scrolling
			if ( this.scroll === 'disable' )
			{	// 
				$(document.body).css('overflow', 'visible');
			}
			
			// We are dead
			this.visible = false;
			
		},
		
		resizeBoxes: function ( type )
		{	// Resize the boxes
			// Used on transition or window resize
			
			// Resize Overlay
			if ( type !== 'transition' )
			{	// We don't care for transition
				var $body = $(this.ie6 ? document.body : document);
				$('#lightbox-overlay').css({
					width:		$body.width(),
					height:		$body.height()
				});
				delete $body;
			}
			
			// Handle cases
			switch ( type )
			{
				case 'general': // general resize (start of lightbox)
					return true;
					break;
				case 'resized': // window was resized
					if ( this.auto_resize === false )
					{	// Stop
						// Reposition
						this.repositionBoxes({'nHeight':nHeight, 'speed':this.speed});
						return true;
					}
				case 'transition': // transition between images
				default: // unknown
					break;
			}
			
			// Get image
			var image = this.images.current();
			if ( !image || !image.width || !this.visible )
			{	// No image or no visible lightbox, so we don't care
				//window.console.warn('A resize occured while no image or no lightbox...');
				return false;
			}
			
			// Resize image box
			// i:image, w:window, b:box, c:current, n:new, d:difference
			
			// Get image dimensions
			var iWidth  = image.width;
			var iHeight = image.height;
			
			// Get window dimensions
			var wWidth  = $(window).width();
			var wHeight = $(window).height();
			
			// Check if we are in size
			// Lightbox can take up 4/5 of size
			if ( this.auto_resize !== false )
			{	// We want to auto resize
				var maxWidth  = Math.floor(wWidth*(4/5));
				var maxHeight = Math.floor(wHeight*(4/5));
				var resizeRatio;
				while ( iWidth > maxWidth || iHeight > maxHeight )
				{	// We need to resize
					if ( iWidth > maxWidth )
					{	// Resize width, then height proportionally
						resizeRatio = maxWidth/iWidth;
						iWidth = maxWidth;
						iHeight = Math.floor(iHeight*resizeRatio);
					}
					if ( iHeight > maxHeight )
					{	// Resize height, then width proportionally
						resizeRatio = maxHeight/iHeight;
						iHeight = maxHeight;
						iWidth = Math.floor(iWidth*resizeRatio);
					}
				}
			}
			
			// Get current width and height
			var cWidth  = $('#lightbox-imageBox').width();
			var cHeight = $('#lightbox-imageBox').height();
	
			// Get the width and height of the selected image plus the padding
			// padding*2 for both sides (left+right || top+bottom)
			var nWidth	= (iWidth  + (this.padding * 2));
			var nHeight	= (iHeight + (this.padding * 2));
			
			// Diferences
			var dWidth  = cWidth  - nWidth;
			var dHeight = cHeight - nHeight;
			
			// Set the overlay buttons height and the infobox width
			// Other dimensions specified by CSS
			$('#lightbox-nav-btnPrev,#lightbox-nav-btnNext').css('height', nHeight); 
			$('#lightbox-infoBox').css('width', nWidth);
			
			// Handle final action
			if ( type === 'transition' )
			{	// We are transition
				// Do we need to wait? (just a nice effect to counter the other
				if ( dWidth === 0 && dHeight === 0 )
				{	// We are the same size
					this.pause(this.speed/3);
					this.showImage(null, 3);
				}
				else
				{	// We are not the same size
					// Animate
					$('#lightbox-image').width(iWidth).height(iHeight);
					$('#lightbox-imageBox').animate({width: nWidth, height: nHeight}, this.speed, function ( ) { $.Lightbox.showImage(null, 3); } );
				}
			}
			else
			{	// We are a resize
				// Animate Lightbox
				$('#lightbox-image').animate({width:iWidth, height:iHeight}, this.speed);
				$('#lightbox-imageBox').animate({width: nWidth, height: nHeight}, this.speed);
			}
			
			// Reposition
			this.repositionBoxes({'nHeight':nHeight, 'speed':this.speed});
			
			// Done
			return true;
		},
		
		repositioning:			false,	// are we currently repositioning
		reposition_failsafe:	false,	// failsafe
		repositionBoxes: function ( options )
		{
			// Prepare
			if ( this.repositioning )
			{	// Already here
				this.reposition_failsafe = true;
				return null;
			}
			this.repositioning = true;
			
			// Options
			options = $.extend({}, options);
			options.callback = options.callback || null;
			options.speed = options.speed || 'slow';
			
			// Get page scroll
			var pageScroll = this.getPageScroll();
			
			// Figure it out
			// alert($(window).height()+"\n"+$(document.body).height()+"\n"+$(document).height());
			// var nHeight = options.nHeight || parseInt($('#lightbox').height(),10) || $(document).height()/3;
			var nHeight = options.nHeight || parseInt($('#lightbox').height(),10);
			
			// Display lightbox in center
			// var nTop = pageScroll.yScroll + ($(document.body).height() /*frame height*/ - nHeight) / 2.5;
			var nTop = pageScroll.yScroll + ($(window).height() /*frame height*/ - nHeight) / 2.5;
			var nLeft = pageScroll.xScroll;
			
			// Animate
			var css = {
				left: nLeft,
				top: nTop
			};
			if (options.speed) {
				$('#lightbox').animate(css, 'slow', function(){
					if ( $.Lightbox.reposition_failsafe )
					{	// Fire again
						$.Lightbox.repositioning = $.Lightbox.reposition_failsafe = false;
						$.Lightbox.repositionBoxes(options);
					}
					else
					{	// Done
						$.Lightbox.repositioning = false;
						if ( options.callback )
						{	// Call the user callback
							options.callback();
						}
					}
				});
			}
			else
			{
				$('#lightbox').css(css);
				if ( this.reposition_failsafe )
				{	// Fire again
					this.repositioning = this.reposition_failsafe = false;
					this.repositionBoxes(options);
				}
				else
				{	// Done
					this.repositioning = false;
				}
			}
			
			// Done
			return true;
		},
		
		visible: false,
		showImage: function ( image, step ){
			// Default step
			step = step || 1;
			
			// Make the image the current image, or get the current
			image = this.images.current(image) || this.images.get('first',true);
			if ( !image ) {
				return;
			}
			
			// What do we need to do
			switch ( step )
			{
				// ---------------------------------
				// We need to preload
				case 1:
				
					// Disable keyboard nav
					this.KeyboardNav_Disable();
					
					// Show the loading image
					$('#lightbox-loading').show();
					
					// Hide things
					$('#lightbox-image,#lightbox-nav,#lightbox-nav-btnPrev,#lightbox-nav-btnNext,#lightbox-infoBox').hide();
					
					// Remove show info events
					$('#lightbox-imageBox').unbind();
					// ^ Why? Because otherwise when the image is changing, the info pops out, not good!
					
					// Check if we need to preload
					if ( image.width && image.height )
					{	// We don't
						// Continue to next step
						this.showImage(null, 2);
					}
					else
					{	// We do
						// Create preloader
						var preloader = new Image();
						// Set callback
						preloader.onload = function()
						{	// We have preloaded the image
							// Update image with our new info
							image.width  = preloader.width;
							image.height = preloader.height;
							// Continue to next step
							$.Lightbox.showImage(null, 2);
							// Kill preloader
							preloader.onload = null;
							preloader = null;
						};
						// Start preload
						preloader.src = image.src;
					}
					
					// Done
					break;
				
				
				// ---------------------------------
				// Resize the container
				case 2:
					
					// Apply image changes
					$('#lightbox-image').attr('src', image.src);
					
					// Set container border (Moved here for Konqueror fix - Credits to Blueyed)
					if ( typeof this.padding === 'undefined' || this.padding === null || isNaN(this.padding) )
					{	// Autodetect
						this.padding = parseInt($('#lightbox-imageContainer').css('padding-left'), 10) || parseInt($('#lightbox-imageContainer').css('padding'), 10) || 0;
					}
					
					// Use colorBlend?
					if ( this.colorBlend )
					{	// We have colorBlend
						// Background
						$('#lightbox-overlay').animate({'backgroundColor':image.color}, this.speed*2);
						// Border
						$('#lightbox-imageBox').css('borderColor', image.color);
					}
					
					// Resize boxes
					this.resizeBoxes('transition');
					// ^ contains callback to next step
					
					// Done
					break;
				
				
				// ---------------------------------
				// Display the image
				case 3:
					
					// Hide loading
					$('#lightbox-loading').hide();
					
					// Animate image
					$('#lightbox-image').fadeIn(this.speed*1.5, function() {
						$.Lightbox.showImage(null, 4);
					});
					
					// Start the proloading of other images
					this.preloadNeighbours();
					
					// Fire custom handler show
					if ( this.handlers.show !== null )
					{	// Fire it
						this.handlers.show(image);
					}
					
					// Done
					break;
				
				
				// ---------------------------------
				// Set image info / Set navigation
				case 4:
					
					// ---------------------------------
					// Set image info
					
					// Hide and set image info
					var $title = $('#lightbox-caption-title').html(image.title || 'Untitled');
					if ( this.download_link ) {
						$title.attr('href', this.download_link ? image.src : '');
					}
					delete $title;
					$('#lightbox-caption-seperator').html(image.description ? ': ' : '');
					$('#lightbox-caption-description').html(image.description || '&nbsp;');
					
					// If we have a set, display image position
					if ( this.images.length > 1 )
					{	// Display
						$('#lightbox-currentNumber').html(this.text.image + '&nbsp;' + ( this.images.index + 1 ) + '&nbsp;' + this.text.of + '&nbsp;' + this.images.length);
					} else
					{	// Empty
						$('#lightbox-currentNumber').html('&nbsp;');
					}
					
					// ---------------------------------
					// Info events
					
					// Apply event
					$('#lightbox-imageBox').unbind('mouseover').mouseover(function(){
						$('#lightbox-infoBox:not(:visible)').stop().slideDown('fast');
					});
					
					// Apply event
					$('#lightbox-infoBox').unbind('mouseover').mouseover(function(){
						$('#lightbox-infoFooter:not(:visible)').stop().slideDown('fast');
					});
					
					// Forced show?
					if ( this.show_extended_info === true )
					{	// Force show
						$('#lightbox-imageBox').trigger('mouseover');
						$('#lightbox-infoBox').trigger('mouseover');
					}
					else if ( this.show_info === true )
					{	// Force show
						$('#lightbox-imageBox').trigger('mouseover');
					}
					
					// ---------------------------------
					// Set navigation
		
					// Instead to define this configuration in CSS file, we define here. And it's need to IE. Just.
					$('#lightbox-nav-btnPrev, #lightbox-nav-btnNext').css({ 'background' : 'transparent url(' + this.files.images.blank + ') no-repeat' });
					
					// If not first, show previous button
					if ( !this.images.isFirst() ) {
						// Not first, show button
						$('#lightbox-nav-btnPrev').show();
					}
					
					// If not last, show next button
					if ( !this.images.isLast() ) {
						// Not first, show button
						$('#lightbox-nav-btnNext').show();
					}
					
					// Make navigation current / show it
					$('#lightbox-nav').show();
					
					// Enable keyboard navigation
					this.KeyboardNav_Enable();
					
					// Done
					break;
					
					
				// ---------------------------------
				// Error handling
				default:
					window.console.error('Don\'t know what to do: ', image, step);
					return this.showImage(image, 1);
					// break;
				
			}
			
			// All done
			return true;
		},
		
		preloadNeighbours: function ( )
		{	// Preload all neighbour images
			
			// Do we need to do this?
			if ( this.images.isSingle() || this.images.isEmpty() )
			{	return true;	}
			
			// Get current image
			var image = this.images.current();
			var index = this.images.index;
			if ( !image ) { return image; }
			var objNext;
			
			// Load previous
			var prev = this.images.prev();
			if ( prev ) {
				objNext = new Image();
				objNext.src = prev.src;
			}
			this.images.setIndex(index); // reset
			
			// Load next
			var next = this.images.next();
			if ( next ) {
				objNext = new Image();
				objNext.src = next.src;
			}
			this.images.setIndex(index); // reset
		},
		
		// --------------------------------------------------
		// Things we don't really care about
		
		KeyboardNav_Enable: function ( ) {
			$(document).keydown(function(objEvent) {
				$.Lightbox.KeyboardNav_Action(objEvent);
			});
		},
		
		KeyboardNav_Disable: function ( ) {
			$(document).unbind('keydown');
		},
		
		KeyboardNav_Action: function ( objEvent ) {
			// Prepare
			objEvent = objEvent || window.event;
			
			// Get the keycode
			var keycode = objEvent.keyCode;
			var escapeKey = objEvent.DOM_VK_ESCAPE /* moz */ || 27;
			
			// Get key
			var key = String.fromCharCode(keycode).toLowerCase();
			
			// Close?
			if ( key === this.keys.close || keycode === escapeKey )
			{	return $.Lightbox.finish();		}
			
			// Prev?
			if ( key === this.keys.prev || keycode === 37 )
			{	// We want previous
				return $.Lightbox.showImage('prev');
			}
			
			// Next?
			if ( key === this.keys.next || keycode === 39 )
			{	// We want next
				return $.Lightbox.showImage('next');
			}
			
			// Unknown
			return true;
		},
		
		getPageScroll: function ( ) {
			var xScroll, yScroll;
			if (self.pageYOffset)
			{	// Some browser
				yScroll = self.pageYOffset;
				xScroll = self.pageXOffset;
			} else if (document.documentElement && document.documentElement.scrollTop)
			{	// Explorer 6 Strict
				yScroll = document.documentElement.scrollTop;
				xScroll = document.documentElement.scrollLeft;
			} else if (document.body)
			{	// All other browsers
				yScroll = document.body.scrollTop;
				xScroll = document.body.scrollLeft;	
			}
			var arrayPageScroll = {'xScroll':xScroll,'yScroll':yScroll};
			return arrayPageScroll;
		},
		
		pause: function ( ms ) {
			var date = new Date();
			var curDate = null;
			do { curDate = new Date(); }
			while ( curDate - date < ms);
		}
	
	}); // We have finished extending/defining our LightboxClass


	// --------------------------------------------------
	// Finish up
	
	// Instantiate
	if ( typeof $.Lightbox === 'undefined' )
	{	// 
		$.Lightbox = new $.LightboxClass();
	}

// Finished definition

})(jQuery); // We are done with our plugin, so lets call it with jQuery as the argument

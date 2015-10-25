function ajax(url, load_func, error_func, handle, form_obj, handle_as, sync, prevent_cache, headers, timeout)
{
	var handle			= ( typeof handle == "undefined" ) ? null : handle;
	var form_obj		= ( typeof form_obj == "undefined" ) ? null : form_obj;
	var handle_as		= ( typeof handle_as == "undefined" ) ? "json" : handle_as;
	var sync			= ( typeof sync == "undefined" ) ? false : sync;
	var prevent_cache	= ( typeof prevent_cache == "undefined" ) ? false : prevent_cache;
	var headers			= ( typeof headers == "undefined" ) ? null : headers;
	var timeout			= ( typeof timeout == "undefined" ) ? 0 : timeout;

	var xhrArgs = {
		url: url,
		form: form_obj,
		load: load_func,
		error: error_func,
		handle: handle,
		handleAs: handle_as,
		sync: sync,
		preventCache: prevent_cache,
		headers: headers,
		timeout: timeout
	}
	var deferred = dojo.xhrPost(xhrArgs);
	return	deferred;
}
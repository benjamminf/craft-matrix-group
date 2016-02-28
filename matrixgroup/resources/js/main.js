window.MatrixGroup = {

	onPropertySet: function(object, property, callback)
	{
		if(object.hasOwnProperty(property))
		{
			callback(object[property], object)
		}
		else
		{
			Object.defineProperty(object, property, {
				configurable: true,

				set: function(value)
				{
					delete object[property]
					object[property] = value

					callback(value, object)
				}
			})
		}
	},

	patchClass: function(Patchee, Patcher, priority, postInit)
	{
		var fn = Patchee.prototype
		var init = fn.init

		if(typeof postInit === 'string')
		{
			postInit = Patcher.prototype[postInit]
		}

		postInit = postInit || function(){}

		fn.init = function()
		{
			var args = Array.prototype.slice.call(arguments)
			var patcher

			if(priority)
			{
				patcher = new Patcher(this, args)
				init.apply(this, args)
			}
			else
			{
				init.apply(this, args)
				patcher = new Patcher(this, args)
			}

			postInit.call(patcher, args)
		}
	},

	patchMethod: function(patchee, patcher, method, priority)
	{
		var superMethod = patchee[method]

		patchee[method] = function()
		{
			var args = Array.prototype.slice.call(arguments)

			if(priority)
			{
				patcher[method](args)
				return superMethod.apply(this, args)
			}
			else
			{
				var output = superMethod.apply(this, args)
				return patcher[method](args, output)
			}
		}
	}
}

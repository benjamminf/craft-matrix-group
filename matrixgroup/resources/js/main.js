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

	patchClass: function(Patchee, Patcher)
	{
		var fn = Patchee.prototype
		var init = fn.init

		fn.init = function()
		{
			init.apply(this, arguments)
			new Patcher(this)
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

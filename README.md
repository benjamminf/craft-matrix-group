# Matrix groups for Craft ![Craft 2.5](https://img.shields.io/badge/craft-2.5-red.svg?style=flat-square)

This Craft CMS plugin will let you recursively nest blocks in your matrix fields.

Has a relatively light touch so it'll work on existing matrix fields, and won't break or destroy your content in the
event the plugin is removed.

That said, this plugin comes with a warning. It is built on undocumented API's and relies on many front-end hacks for it
to function. That means there is always the risk it could break on future Craft CMS updates. Use at your own risk.


## Usage

### Fields

When editing Matrix fields, you'll be presented with a new option when creating and editing block types. Ticking this
checkbox will set the block type as a "group" type. Along with whatever fields you assign to this block type, you will
also now be able to nest blocks within it.

### Templates

The plugin comes with it's own custom Twig tag, which works the same way as Craft's
[`{% nav %}`](https://craftcms.com/docs/templating/nav) tag:
```twig
<ul>
	{% matrixgroup block in entry.matrixField %}
		<li>
			{{ block.type.name }}
			{% ifchildren %}
				<ul>
					{% children %}
				</ul>
			{% endifchildren %}
		</li>
	{% endfor %}
</ul>
```

 You can also accomplish this in a non-recursive manner:
```twig
<ul>
	{% for block in craft.matrixGroup.topLevel(entry.matrixField) %}
		<li>
			{{ block.type.name }}
			{% set children = craft.matrixGroup.children(block) %}
			{% if children|length > 0 %}
				{# ... #}
			{% endif %}
		</li>
	{% endfor %}
</ul>
```


## API

### `{% matrixgroup %}`

Refer to Craft's documentation of [`{% nav %}`](https://craftcms.com/docs/templating/nav), as they are essentially
identical in usage and function. Just replace `nav` with `matrixgroup` and you're good to go!

### `craft.matrixGroup.*`

| Function           | Description                                                                                                                |
|--------------------|----------------------------------------------------------------------------------------------------------------------------|
| `topLevel(blocks)` | Takes in an array of `MatrixBlockModel`'s and filters it to only the top-level ones.                                       |
| `children(block)`  | Takes in a `MatrixBlockModel` instance and returns an array of `MatrixBlockModel`'s that are children of the passed block. |

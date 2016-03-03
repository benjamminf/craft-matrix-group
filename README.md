# Matrix groups for Craft ![Craft 2.5](https://img.shields.io/badge/craft-2.5-red.svg?style=flat-square)

This Craft CMS plugin will let you recursively nest blocks in your matrix fields. Has a relatively light touch so it'll
work on existing matrix fields, and won't break or destroy your content in the event the plugin is removed.

## Usage

### Templates

Works the same way as the `nav` tag:
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

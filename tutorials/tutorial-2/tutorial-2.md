Table of Contents
-----------------
- [Selectors](#selectors)
- [Scope and nesting](#scope-and-nesting)

The basics
-----------------------------------

### <a name="selectors"></a>Selectors
Selectors point to the elements in your XML file, to tell the rendering engine
which properties to apply to which object. Create a new text file `/basics.xml`
and type the following:

	<?xml version="1.0" encoding="UTF-8"?>
	<?xml-stylesheet href="basics.hss" type="application/x-hss" version="1.0"?>
	<example>
		<sibling>Element A</sibling>
		<sibling>Element B</sibling>
	</example>

This basic XML file contains a root element called `example`, which contains two
children elements, called `sibling`, which in turn contain `"Element A"` and
`"Element B"` as content text

Create the HSS file: `/basics.hss`, containing the following:

	*
	{
		background: #D;
	}

If you went trough the last tutorial you can probably guess that this is a rule,
and that what's inside the block applies a gray background. The difference is
that instead of targeting the root element with its name, we are using the
universal selector, the star `*`. Open the `/basics.xml` file with the prototype
and you should see something like the following:

![The whole window is filled with gray, plus some basic text](img/2001.jpg?raw=1)

### <a name="scope-and-nesting"></a>Scope and nesting
If you are familiar with CSS, you may think that this applies to all elements in
the XML source. There is a crucial difference in HSS, and that is the notion of
scope. In the example, only the root element will be selected, because we always
start at root scope. To select elements further down the document tree, you need
to use nesting or combinators (these will be explored later). The scope
restricts what will be selected, and which elements are said to be in scope
depends on where in your HSS file you use a given selector.

In HSS you can put rules inside other rules, this is what's called nesting.
Change your `/basics.hss` file to look like this:

	*
	{
		background: #D;

		sibling
		{
			width: 100;
			height: 100;
			alignX: center;
			alignY: middle;
			contentAlignY: middle;
			textAlign: center;
			background: #0003;
			border: @{
				size: 1;
				color: #7;
			};
		}
	}

Reload the file and you should see something like this:

![The children are squares and placed next to each other](img/2002.jpg?raw=1)

Here's what we added:

	sibling
	{
		width: 100;
		height: 100;
		alignX: center;
		alignY: middle;
		contentAlignY: middle;
		textAlign: center;
		background: #0003;
		border: @{
			size: 1;
			color: #7;
		};
	}

We are selecting all the `sibling` elements inside the previously selected
element, and adding some properties to be able to see them better. Only the two
children of the `example` element are in scope, because we are inside the rule
that selects the `example` element.

### Dissection
Let's go over the property definitions and see what they do:

	width: 100;
	height: 100;

Here we are setting the basic dimensions of the elements to 100 points. These
are resolution independent units of measure, which when the scale factor is 1
are equal to 1 screen pixel. The `width` and `height` properties accept
numbers, percentage numbers or the keyword `content` (plus some advanced things
we won't consider right now). Anywhere numbers are accepted you can also write
expressions, using common mathematical operands, such as `+`, `-`, `*` and `/`.
Parentheses are also allowed, and they are very useful to group expressions.

The default value for `width` is the percentage number `100%` and for `height`
it is the keyword `content`, therefore making them as wide as the parent and as
tall as their content when left to their default values.

Let's experiment a bit: Change the width to 50%. Now your document should look
like this:

![The columns take up the entire width of the window](img/2003.jpg?raw=1)

Now we want to make them as tall as the window, so we set the height to 100%.

![The columns take up the entire height of the window](img/2004.jpg?raw=1)

Maybe not completely as tall as the window. Make it 100% - 30. If you resize the
window you'll see that the empty space at the top and bottom will always be 15
points each.

![There is a 15 points gap at the top and at the bottom](img/2005.jpg?raw=1)

Just to brag, now, we're going to take it a bit to extreme! Make the width
`(100% - (10% + 5))/2`:

![It works! - There is now a gap at each side, too.](img/2006.jpg?raw=1)

OK, this is probably overdoing it :) But this is just to show that you can use
more complicated expressions without problems.

Let's go back to `width: 100; height: 100;` and then look at the next lines:

	alignX: center;
	alignY: middle;

Here we are setting the alignment point in the horizontal axis to the keyword
`center` and in the vertical axis to `middle`, both of which will be automatically
converted to `50%`.

If we change the `alignY` to `top` or `0`, you'll see it sticks to the top:

![The elements are top aligned](img/2007.jpg?raw=1)

If we set it to `bottom` or `100%`, it will go to the bottom:

![The elements are bottom aligned](img/2008.jpg?raw=1)

Intermediate values are possible, as well, of course. Apart from the keywords,
you can use percentages and plain numbers. Percentages refer to the inner
dimensions of the containing object, which means the size minus the paddings.

Experiment with the dimensions and alignment and then go back to the original
values. Then, let's have a look at the next lines:

	contentAlignY: middle;
	textAlign: center;

This is to center the content text inside each of the elements. The text object
is placed at the vertical center by means of layout, and the actual words are
just centered like in a text editor.

	background: #0003;

In this case, we are defining the background color of the element, by using the
`background` property and assigning it a hexadecimal color value. You'll
probably know this kind of notation from programs such as Photoshop or similar.

You may be wondering, why does `#0003` give a gray color? For that, let's study
all the different options we have when defining a color with hexadecimal notation.
After the "hash sign" comes values for the different channels that compose a color:

- 1 digit: Grayscale, with 16 possible values of lightness.
- 2 digits: Grayscale too, but with two digit precision, allowing 256 values.
- 3 digits: RGB, with one digit per channel.
- 4 digits: RGBA, like the last one, but with an Alpha channel too. Therefore, the
fourth digit is transparency.
- 5 digits: RGBAA, one digit per channel for the color, two digits for the alpha.
- 6 digits: RRGGBB, two digits per channel, opaque.
- 7 digits: RRGGBBA, to digits per channel with one digit alpha.
- 8 digits: RRGGBBAA, the full blown one, with two digits per channel for the color
and the transparency too.

Therefore, we can conclude that what we are seeing is actually not gray, but black
(`#000`), with the value `3` for transparency.

Finally, the border:

	border: @{
		size: 1;
		color: #7;
	};

See the `@` there? It indicates that what we are using is an HSS object. In this
case, we have omitted the object type, because we are using the default type for
the border property, which is `@lineBorder` (FIXME). Therefore, the following
would be absolutely equivalent:

	border: @lineBorder {
		size: 1;
		color: #7;
	};

But since it's quite redundant, and the default type fits our needs, we just skip
it. Inside of the block, we are defining the border to be 1 unit wide and of dark
gray color.

Experiment a bit with the properties we have learned in this section until you are
satisfied, and then let's move on to build something prettier :)

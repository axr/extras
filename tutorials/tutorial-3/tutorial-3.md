Table of Contents
-----------------
- [Basic Styling](#basic-styling)
- [Modularize!](#modularize)
- [Elements are objects, too](#elements-are-objects-too)
- [Modularizing colors](#modularizing-colors)
- [Glossin' it up](#glossin-it-up)
- [More bells and whistles](#more-bells-and-whistles)
- [Hover State](#hover-state)
- [Press State](#press-state)
- [Final Result](#final-result)

A fancy button
--------------

Ok let's do something a bit more advanced. We're going to create a button, with
some different states.

Let's start with some basic XML. Create a new file called `/fancy_button.xml` and
then write this:

	<?xml version="1.0" encoding="UTF-8" ?>
	<?xml-stylesheet href="fancy_button.hss" type="application/x-hss" version="0.4.8" ?>
	<document>
		<link>Click me!</link>
	</document>

Create the HSS file: `/fancy_button.hss`, containing the following as a starting
point:

	*
	{
		background: #D;
	}

As a reminder, we are targeting the root element with the universal selector `*`,
and since at the base level only this one is in scope, it will be the only element
selected. You'll see the document with a gray background:

![The starting point: basic text with gray background](img/3001.jpg?raw=1)

### <a name="basic-styling"></a>Basic styling

Now, let's give the button some basic dimensions and some rudimentary styles:

    *
    {
        background: #D;

        link
        {
            width: 150;
            height: 60;
            alignX: center;
            alignY: middle;
            contentAlignY: middle;
            textAlign: center;
            background: #FE2;
            font: @font {
                face: "Helvetica";
                color: #D90;
            };
        }
    }

You'll see something like this:

![The button is now yello and centered in the window](img/3002.jpg?raw=1)

Let's go over the property definitions and see what they do. Here we are setting the
basic dimensions of the element, and aligning it in the center of the page:

	width: 150;
	height: 60;
	alignX: center;
	alignY: middle;

We align the content text in the vertical center of the button, and set the text to
be center-aligned, like in a text processor:

	contentAlignY: middle;
	textAlign: center;

We set the background to be a yellow color:

	background: #FE2;

Finally, we change the font, to be a bit cleaner, and use a color that fits better
in the color scheme.

	font: @font {
		face: "Helvetica";
		color: #D90;
	};

### <a name="modularize"></a>Modularize!

We started applying the styles directly to the element, but to make the style more
reusable, this is a good moment to split out the code and modularize it.

To better understand this, let's first discuss how objects work in HSS. Everything
in HSS is an object: every element, every color... every value is an object, even
though many times you will be using literal values, like a string or a number,
directly. For more complex values, you use what's called an "object definition",
like when defining a font or a shape.

All objects have a type, an "object type" to be precise. This is what comes after
the `@` when writing an object definition, although it can be skipped in some
circumstances. They can also optionally have a name, but keep in mind that up
until now we have always used them in their anonymous form.

Therefore, when you write an object definition, you are providing a template with
which a new object can be created, which happens when you pass it by name to a
property, or you inherit from it by using the `isA` property.

Let's begin with a simple example, which we have actually already used in our
code. Here we are passing an anonymous `@font` object to the `font` property of
the `foo` element. We could put the object definition outside of the rules (e.g.
at the very beginning of the file), and then pass it using its name:

    foo
    {
        font: @font {
        	face: "Helvetica";
        	color: #D90;
        };
    }

Notice how after the object sign and type (`@font`), there is the object name
(`myFont`), which is then written as the value for the `font` property:

    @font myFont
	{
    	face: "Helvetica";
    	color: #D90;
    }
    
    element
    {
        font: myFont;
    }

Now, if you want to make one object inherit from another, you can use the `isA`
property, which exists on all objects:

    @font myFont
	{
    	face: "Helvetica";
    	color: #D90;
    }

	@font myFont2
	{
		isA: myFont;
		size: 20;
	}

This way, `myFont2` will have all the properties of `myFont`, and additionally
`size: 20`. You can also overwrite the ones you've previously inherited.

### <a name="elements-are-objects-too"></a>Elements are objects, too

Now that we've come up to this point, you need to know that for each node in the
XML tree that has been read, an HSS object of type `@container` will be created.
This means that the same rules apply for them as for any other object, like the
inheritance we just discussed.

So, back to our fancy button. We take most of the properties out of the rule and
put them in an object definition, which we apply with `isA`, like so:

    @container fancyButton
    {
        contentAlignY: middle;
        background: #FE2;
        textAlign: center;
        font: @font {
            face: "Helvetica";
            color: #D90;
        };
    }

    *
    {
        background: #D;

        link
        {
            isA: fancyButton;
            width: 150;
            height: 60;
            alignX: center;
            alignY: middle;
        }
    }

If you reload the document you will see... no change at all! But what we achieved
with this is that if we were to apply the style to another element, we wouldn't
need to copy paste or group selectors together, but we would just apply it by
its name. We just made it modular.

### <a name="modularizing-colors"></a>Modularizing colors

As we discussed earlier, everything is an object. When we write a number sign
followed by a hexadecimal number, we are expressing what is called a "color
instruction". When the HSS parser encounters one of these, it actually creates an
`@rgb` object on the fly and returns that one instead.

The syntax of the color instructions allow for using a name too, which will be the
object name of that generated color object. You just write the value followed by a
space and the desired name, like so:

	#D mainBgColor;

This is equivalent to:

	@rgb mainBgColor
	{
		red: 221;
		green: 221;
		blue: 221;
		alpha: 100%;
	}

So what we are going to do now is to take out all the colors that have been used
directly, and define them at the top of the file:

    #D mainBgColor;
    #FE2 buttonColorBase;
    #D90 buttonFontColor;
    
    @container fancyButton
    {
        contentAlignY: middle;
        background: buttonColorBase;
        textAlign: center;
        font: @font {
            face: "Helvetica";
            color: buttonFontColor;
        };
    }

    *
    {
        background: mainBgColor;

        link
        {
            isA: fancyButton;
            width: 150;
            height: 60;
            alignX: center;
            alignY: middle;
        }
    }

Again, if we reload the file nothing has changed, but we are doing this to be as
reusable as possible. Eventually an entire framework arises out of various
components that are defined and used throughout the entire site.

### <a name="glossin-it-up"></a>Glossin' it up

Let's add a dash of "web2.0" to the button style: let's make the button glossy!
For this we are going to take advantage of the ability to overlay multiple
objects as background for our container, so we are going to define a linear
gradient that goes from top to bottom, that does a hard step to more transparent
in the middle, giving it the impression that it is reflective:

    @linearGradient gloss
    {
        startColor: #FFF3;
        colorStops: #FFF6, transparent;
        endColor: transparent;
        endY: 100%;
    }

Let's dissect this: The gradient object has by default `startX`, `startY`,
`endX` and `endY` all `0`. Therefore if we want to make it go from top to
bottom we leave everything as it is except the `endY` property, which will be
placed all the way to the bottom, therefore `100%`.

We start at `#FFF3`, which is a quite transparent white. Then we add two color
stops, which, since we want them both at exactly the center, we just use colors
directly. We could have also used `@colorStop { color: #FFF6; position: 50% }`,
but that's way more verbose, and the default position suits us just fine. Since
both color stops are placed at the same spot, it will jump from the
semitransparent white to the fully transparent color immediately. Notice also
how the `transparent` keyword correctly creates a color that is that is the
same as the surrounding one(s) (`#FFF`) and with alpha `0`, not just black
transparent, which would look very ugly in this case.

Finally, we just continue fully transparent until the bottom.

Now we just need to apply the gradient on top of the background color:

	background: buttonColorBase, gloss;

We get something that looks like this:

![The button is now glossy](img/3003.jpg?raw=1)

The full code is now:

    #D mainBgColor;
    #FE2 buttonColorBase;
    #D90 buttonFontColor;
    
    @linearGradient gloss
    {
        startColor: #FFF3;
        colorStops: #FFF6, transparent;
        endColor: transparent;
        endY: 100%;
    }
    
    @container fancyButton
    {
        contentAlignY: middle;
        background: buttonColorBase, gloss;
        textAlign: center;
        font: @font {
            face: "Helvetica";
            color: buttonFontColor;
        };
    }

    *
    {
        background: mainBgColor;

        link
        {
            isA: fancyButton;
            width: 150;
            height: 60;
            alignX: center;
            alignY: middle;
        }
    }

### <a name="more-bells-and-whistles"></a>More bells and whistles

Alright, let's round it off: we're going to round those pointy corners
and add borders to give it some volume.

Add the following to the `fancyButton` object definition:

	shape: @roundedRect { corners: 10 };

This will make the object use a shape that draws round corners, using
the given size as the radius.

Since this is pretty verbose, we're going to use advantage of the default
object type and shorthand notation to cut it down. The following is equivalent:

	shape: @{ 10 };

It will look like this:

![With rounded corners](img/3004.jpg?raw=1)

Now this still looks quite flat, let's add some borders to create a bevel
effect. First we define the colors:

	#FFFA buttonHighlightColor;
	#D909 buttonShadowColor;

Then, we define the borders, directly on the `fancyButton` object definition:

	border: @lineBorder {
		size: 1;
		color: buttonHighlightColor;
		position: inside;
	}, @lineBorder {
		size: 1;
		color: buttonShadowColor;
		position: inside;
	};

This adds to thin borders to the inside of the shape. It will now look
something like this:

![Now with borders, too](img/3005.jpg?raw=1)

To add even more, let's make something that looks sort of like a drop
shadow. Add one more border after the other two, this time to the outside
of the shape:

	@lineBorder {
		size: 10;
		color: #00008;
		position: outside;
	}

The entire code now looks like this:

	#D mainBgColor;
	#FE2 buttonColorBase;
	#D90 buttonFontColor;
	#FFFA buttonHighlightColor;
	#D909 buttonShadowColor;

	@linearGradient gloss
	{
		startColor: #FFF3;
		colorStops: #FFF6, transparent;
		endColor: transparent;
		endY: 100%;
	}

	@container fancyButton
	{
		contentAlignY: middle;
		background: buttonColorBase, gloss;
		textAlign: center;
		font: @
		{
			face: "Helvetica";
			color: buttonFontColor;
			weight: normal; //FIXME
		};
		shape: @{ 10 };
		
		border: @lineBorder {
			size: 1;
			color: buttonHighlightColor;
			position: inside;
		
		}, @lineBorder {
			size: 1;
			color: buttonShadowColor;
			position: inside;

		}, @lineBorder {
			size: 10;
			color: #00008;
			position: outside;
		};
	}

	*
	{
		background: mainBgColor;

		link
		{
			isA: fancyButton;
			width: 150;
			height: 60;
			alignX: center;
			alignY: middle;
		}
	}

The result:

![With a soft shadow effect](img/3006.jpg?raw=1)

### <a name="hover-state"></a>Hover state

Each element stores so called "flags", that determine what state they're in. Think of them sort of like classes in CSS. They can be turned on and off at will, or automatically by the rendering engine, as you will see in a minute. For how to control flags defined by the author, refer to a more advanced tutorial.

Alright, now we're going to add some interactivity to the button, by changing
the background color when the mouse is placed over it. First, we define the color:

	#FFF340 buttonColorHover;

Then, we target the link element only when the hover flag is active. Add the
following after the rule that targets `link`:

	link::hover
	{
		background: buttonColorHover, gloss;
	}

Now when you place your mouse cursor over the button, it will look like this:

![On hover, a slightly brighter yellow](img/3007.jpg?raw=1)

You may not be able to see the difference right here on the screenshot, but it
is immediately visible when seeing it live.

The important part of the syntax to note is the double colons `::`, followed by the flag name as an identifier `hover`. This is a system-provided flag, so it will be automatically turned on and off as you pass the mouse pointer over the elements.

#### Modularizing flags

Now, this is not the most optimal way to do it, in this situation, since we are defining the flag outside of the object definition. We want this hover state to be applied to all elements where we have applied the `fancyButton` object definition, so we are going to use rules inside object definitions.

Basically, it's the same concept as with nested rules, you put the rules that apply to the children (or other relation) inside the definition object, and when applying it, the entire structure will be affected. Keep in mind that this only makes sense with object types can have children (with some exceptions that will be covered in a more advanced tutorial).

So inside the `fancyButton` object definition, we're going to add a rule that targets itself, using the `@this` "reference object", combined with the hover flag as before.

	@container fancyButton
	{
		contentAlignY: middle;
		background: buttonColorBase, gloss;
		...
	
		@this::hover
		{
			background: buttonColorHover, gloss;
		}
	}

To make it more convenient, the default object type in a selector is `this`, therefore you can skip the word:

	@::hover
	{
		background: buttonColorHover, gloss;
	}

If you forget the `@`, remember that it would be interpreted as `*::hover`, targeting the children instead.

The entire code now looks like this:

	#D mainBgColor;
	#FE2 buttonColorBase;
	#FFF340 buttonColorHover;
	#D90 buttonFontColor;
	#FFFA buttonHighlightColor;
	#D909 buttonShadowColor;

	@linearGradient gloss
	{
		startColor: #FFF3;
		colorStops: #FFF6, transparent;
		endColor: transparent;
		endY: 100%;
	}

	@container fancyButton
	{
		contentAlignY: middle;
		background: buttonColorBase, gloss;
		textAlign: center;
		font: @font {
			face: "Helvetica";
			color: buttonFontColor;
			weight: normal; //FIXME
		};
		shape: @roundedRect { 10 };
	
		border: @lineBorder {
			size: 1;
			color: buttonHighlightColor;
			position: inside;
		
		}, @lineBorder {
			size: 1;
			color: buttonShadowColor;
			position: inside;
		
		}, @lineBorder {
			size: 10;
			color: #00008;
			position: outside;
		};
	
		@::hover
		{
			background: buttonColorHover, gloss;
		}
	}

	*
	{
		background: mainBgColor;

		link
		{
			isA: fancyButton;
			width: 150;
			height: 60;
			alignX: center;
			alignY: middle;
		}
	}

Now, whenever the `fancyButton` is applied, you get hovers too!

### <a name="press-state"></a>Press state

To finish off this tutorial, lets add some feedback for when the button is actually pressed with the mouse, so the user knows that the button was successfully clicked. We'll use the system-provided `press` flag.

We add yet another color:

	#FD2 buttonColorPress;
	

And we add the rule with the flag, right after the hover one:

	
	@container fancyButton
	{
		contentAlignY: middle;
		background: buttonColorBase, gloss;
		...
	
		@::hover
		{
			background: buttonColorHover, gloss;
		}

		@::press
		{
			background: buttonColorPress, gloss;
		}
	}
	
So now when the button is pressed, it looks like this:

//FIXME: insert image here

### <a name="final-result"></a>Final Result

The fully completed code looks like this:


	
	#D mainBgColor;
	#FE2 buttonColorBase;
	#FFF340 buttonColorHover;
	#FD2 buttonColorPress;
	#D90 buttonFontColor;
	#FFFA buttonHighlightColor;
	#D909 buttonShadowColor;

	@linearGradient gloss
	{
		startColor: #FFF3;
		colorStops: #FFF6, transparent;
		endColor: transparent;
		endY: 100%;
	}

	@container fancyButton
	{
		contentAlignY: middle;
		background: buttonColorBase, gloss;
		textAlign: center;
		font: @font {
			face: "Helvetica";
			color: buttonFontColor;
			weight: normal; //FIXME
		};
		shape: @roundedRect { 10 };
	
		border: @lineBorder {
			size: 1;
			color: buttonHighlightColor;
			position: inside;
		
		}, @lineBorder {
			size: 1;
			color: buttonShadowColor;
			position: inside;
		
		}, @lineBorder {
			size: 10;
			color: #00008;
			position: outside;
		};
	
		@::hover
		{
			background: buttonColorHover, gloss;
		}
	
		@::press
		{
			background: buttonColorPress, gloss;
		}
	}

	*
	{
		background: mainBgColor;

		link
		{
			isA: fancyButton;
			width: 150;
			height: 60;
			alignX: center;
			alignY: middle;
		}
	}
	

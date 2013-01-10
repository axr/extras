Table of Contents
-----------------
- [Introduction](#introduction)
- [Set up](#setup)
- [Hello world](#hello-world)

<a name="introduction"></a>Introduction
---------------------------------------
HSS is the design language that powers the presentational side of a document
written for the AXR platform. Its syntax is inspired in CSS, but it takes the
concept to a whole new level, providing many new features that enable newfound
flexibility and power.

It was invented by Miro Keller in 2009 and has been evolving since then. The
idea started as a small superset of CSS, but it became clear to the author that
a fresh start would be required because the burden of legacy compatibility was
too high to create a really great language. Thus, the AXR Project was started.

<a name="setup"></a>Set-up of the environment
---------------------------------------------
At the time of writing this tutorial, the prototype rendering engine is capable
of handling all the concepts we are going to explore, but it is not available as
a browser plug-in, yet. Thus, you need to download the test browser app for your
specific platform from the [project page](http://axr.vg/), and open XML and HSS
files located on your local computer.

To do so, point your favorite browser to the [AXR Project's website](http://axr.vg/)
and click on the big download button over there. This should download the
appropriate version of the AXR Browser for your operating system. If that
for whatever reason doesn't work, you can go directly to the 
[downloads page](http://axr.vg/downloads/) and get it from there.

Once you've downloaded it, if you are on Windows, run the installer; on Linux,
install the appropriate package for your distro or extract the TGZ if your OS uses
a different package management system; or, in case of Mac OSX, mount the DMG and drag
to the Applications folder. Then, open the AXR Browser application.

Next, you should create a folder somewhere you can remember, and create the text
files that we are going to discuss in these tutorials. The best way is to
manually type the things in yourself, instead of copying and pasting, because
that way you'll learn much more and you'll be much less prone to overlooking
details of the syntax.

For the sake of simplicity, I'll just use paths that start with a slash as if
the files were located in the root of your hard-drive. Please prepend them with
what's missing. Say, for example that I put my files in a new folder on the
desktop, called tutorials. Whenever I see `/index.xml` in the tutorial, the
actual path to that file would be `/Users/Axerr/Desktop/tutorials/index.xml`,
since I am on a Unix-like system.

Configure your text editor to use the UTF-8 encoding, either at the time of the
file creation or in a setting in your application's settings. This encoding is
the official recommended one to use.

<a name="hello-world"></a>Hello world
-------------------------------------
The first thing we'll do, to follow the tradition of all programming languages,
is creating the most basic document possible, which gives a basic output.

### Let's rock!
Open your favorite text editor, and create a new file `/hello_world.xml`. Inside
it, write this:

	<?xml version="1.0" encoding="UTF-8" ?>
	<hello>Hello world</hello>

Done! Now, open it in the prototype, and you'll see something like this:

![Prototype showing Hello world](img/1001.jpg?raw=1)

Let's go over each line:

	<?xml version="1.0" encoding="UTF-8" ?>

This is an XML instruction that needs to be present in all XML files, as per the
[specification](http://spec.axr.vg/). We declare the file to use the 1.0 version
of the XML standard, and that our file is encoded in the UTF-8 encoding, which
is what you should always use.

	<hello>Hello world</hello>

The only requirement for the XML file that the AXR platform imposes is that the
XML document is
[well-formed](http://en.wikipedia.org/wiki/XML#Well-formedness_and_error-handling),
and that means, among other things, that there must be one, and only one, root
element that contains all the other elements. In this case our root element is
called `hello`, and it contains just some content text, `Hello world`.

### Style sheets
Now, let's style it up a bit. For that, we need to add a line to the XML file
that will determine how to find the stylesheet that will contain the
instructions on how to represent the content of the XML file:

	<?xml version="1.0" encoding="UTF-8"?>
	<?xml-stylesheet href="style.hss" type="application/x-hss" version="0.4.8" ?>
	<hello>Hello world</hello>

Here's what we added:

	<?xml-stylesheet href="style.hss" type="application/x-hss" version="0.4.8"?>

This line is a so called XML instruction, and the instruction name is
`xml-stylesheet`. Its arguments, `href`, `type` and `version` can come in any
order.

- `href` is the location of the HSS file relatively to the XML file. No absolute
  paths or URLs are supported yet in the prototype.

- `type` is the MIME type of the stylesheet. In this case, we are using
  `application/x-hss`, because it is not an official standard yet, and MIME
  rules state that you have to use the `x-` prefix for non-standard types. In
  the future, this may end up being `application/hss` or even `text/hss`

- Last, but not least, it is always required that you declare the `version` of
the AXR platform that you are targeting. In case that the syntax changes in the
future, backwards compatibility modules will be able to render old documents
correctly, becaue they will know what you meant in the first place.

Now, with your favorite text editor, create a new file `/style.hss`, and type
this:

	hello
	{
		background: #ED;
		textAlign: center;
		contentAlignY: middle;
	}

Go back to the prototype, hit refresh (cmd+R on the Mac, ctrl+R or F5 on Windows
and Linux), and you should get something like this:

![Prototype showing styled up Hello world](img/1002.jpg?raw=1)

Let's go over each part:

	hello
	{
	
	}

This is what's called a rule. It begins with a selector, which points to an
element in the XML source tree, which will receive whatever is defined inside of
the block (which starts with `{` and ends with `}`).

	background: #ED;

This is a property definition. First comes the property name, then immediately
after it comes a colon, then optional whitespace, then one or more values, then
more optional whitespace, and finally an end of statement (aka semicolon), which
is optional when the property definition is the last thing in the rule.

In this particular line, we are setting the `background` property of the
`hello` element to a light gray (in case, you haven't guessed it, `#ED`
represents a color).

	textAlign: center;

Here we are setting the content text to be center aligned, as you'd do in any
text processor.

	contentAlignY: middle;

And finally, here comes a small taste of the magic of HSS. How many times have
you wanted to do this with CSS? You set the content of the `hello` element to
be vertically aligned to the middle, which is equivalent to `50%`. We'll get to
it later in more detail, but for now, know that the root element will always be
as wide and tall as the window.

### Final touch
To wrap up this segment, let's change the font of the text. We are going to use
what's called an object definition, which will be explained more in depth in
further chapters. For now, just copy along. Add the following lines to the
block:

	font: @{
		face: "Impact";
		size: 40;
		color: #C;
	};

The entire file `/style.hss` now looks like this:

	hello
	{
		background: #ED;
		textAlign: center;
		contentAlignY: middle;
		font: @{
			face: "Impact";
			size: 40;
			color: #C;
		};
	}

If you reload in the prototype, you should get something like this:

![Prototype showing the final Hello world example](img/1003.jpg?raw=1)

You can recognize a HSS object by the `@`, which is called the object sign. Its
block contains property definitions, like normal rules do. In this concrete
example, the properties `face`, `size` and `color` are the ones we use because
we are defining a `@font` object, which is the default object type for the
`font` property.

If you didn't fully understand what's going on, don't worry. It will become much
clearer as we dive into the full syntax of objects and how to use them.
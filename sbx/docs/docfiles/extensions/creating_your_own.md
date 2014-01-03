/*
Title: Creating Your Own SBX Extension
Description: Information on creating your own SBX extension
Author: Michael Beckwith
Date: 01-02-2014
Last Edited: 01-02-2014
 */

# Extending SBX

While SBX may be powerful and handle a lot of your needs for you, out of the box, you may find moments where it is just not quite meeting your needs. However, you know how to code the features yourself. Being the smart developer you are, you know not to go editing the SBX framework directly.

SBX does try to handle its fair weight, but realistically it can not handle every single use case someone may need. Because of this, SBX tries to offer plenty of hooks to allow customization of experience. With these hooks, you can alter the behavior and content that go into the theme and the website. Many of the functions are also "pluggable" as they are wrapped in `function_exists()` if statements. With these, you should be able to start extending SBX in new ways. If you are finding yourself in a situation where you need a hook in a place that there is none, please reach out to us with your needs, so that we can look into possibly adding a new hook for you.

/**
 * Interaction for the core
 *
 * @author        Ken Depelchin <ken.depelchin@gmail.com>
 */
core =
{
    // init, something like a constructor
    init: function()
    {
        core.controls.init();
    }
}

core.controls =
{
    init: function()
    {

        core.controls.menuTabs();
    },

    menuTabs: function()
    {
    }
}

$(core.init);

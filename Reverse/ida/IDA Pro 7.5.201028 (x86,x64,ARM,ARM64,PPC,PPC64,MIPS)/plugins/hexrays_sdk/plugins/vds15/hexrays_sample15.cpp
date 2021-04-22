/*
 *      Hex-Rays Decompiler project
 *      Copyright (c) 2007-2020 by Hex-Rays, support@hex-rays.com
 *      ALL RIGHTS RESERVED.
 *
 *      Sample plugin for Hex-Rays Decompiler.
 *      It shows known value ranges of a register using get_valranges().
 *
 *      Unfortunately this plugin is of limited use because:
 *        - simple cases where a single value is assigned to a register
 *          are automatically handled by the decompiler and the register
 *          is replaced by the value
 *        - too complex cases where the register gets its value from untrackable
 *          sources, it fails
 *        - only value ranges at the basic block start are shown
 */


#include <hexrays.hpp>
#include <frame.hpp>

// Hex-Rays API pointer
hexdsp_t *hexdsp = NULL;

//--------------------------------------------------------------------------
struct plugin_ctx_t : public plugmod_t
{
  plugin_ctx_t() {}
  ~plugin_ctx_t()
  {
    if ( hexdsp != nullptr )
      term_hexrays_plugin();
  }
  virtual bool idaapi run(size_t) override;
};

//--------------------------------------------------------------------------
bool idaapi plugin_ctx_t::run(size_t)
{
  ea_t ea = get_screen_ea();
  func_t *pfn = get_func(ea);
  if ( pfn == NULL )
  {
    warning("Please position the cursor within a function");
    return true;
  }

  flags_t F = get_flags(ea);
  if ( !is_code(F) )
  {
    warning("Please position the cursor on an instruction\n");
    return true;
  }

  gco_info_t gco;
  if ( !get_current_operand(&gco) )
  {
    warning("Could not find a register or stkvar in the current operand");
    return true;
  }

  // generate microcode
  hexrays_failure_t hf;
  mba_ranges_t mbr(pfn);
  mba_t *mba = gen_microcode(mbr, &hf, NULL, DECOMP_WARNINGS);
  if ( mba == NULL )
  {
    warning("%a: %s", hf.errea, hf.desc().c_str());
    return true;
  }

  // prepare mlist for the current operand
  mlist_t list;
  if ( !gco.append_to_list(&list, mba) )
  {
    warning("Failed to represent %s as microcode list", gco.name.c_str());
    delete mba;
    return false;
  }

  op_parent_info_t ctx;
  mop_t *mop = mba->find_mop(&ctx, ea, gco.is_def(), list);
  if ( mop == NULL )
  {
    warning("Could not find %s in the microcode, sorry\n"
            "Probably it has been optimized away\n", gco.name.c_str());
    delete mba;
    return false;
  }

  qstring opname;
  mop->print(&opname, SHINS_SHORT);
  tag_remove(&opname);

  valrng_t vr;
  int vrflags = VR_AT_START | VR_EXACT;
  if ( ctx.blk->get_valranges(&vr, vivl_t(*mop), ctx.topins, vrflags) )
  {
    qstring vrstr;
    vr.print(&vrstr);
    warning("Value ranges of %s at %a: %s",
            opname.c_str(),
            ctx.blk->start,
            vrstr.c_str());
  }
  else
  {
    warning("Cannot find value ranges of %s", opname.c_str());
  }

  // We must explicitly delete the microcode array
  delete mba;
  return true;
}

//--------------------------------------------------------------------------
static plugmod_t *idaapi init()
{
  if ( !init_hexrays_plugin() )
    return nullptr; // no decompiler
  const char *hxver = get_hexrays_version();
  msg("Hex-rays version %s has been detected, %s ready to use\n",
      hxver, PLUGIN.wanted_name);
  return new plugin_ctx_t;
}

//--------------------------------------------------------------------------
static const char comment[] = "Sample15 plugin for Hex-Rays decompiler";

//--------------------------------------------------------------------------
//
//      PLUGIN DESCRIPTION BLOCK
//
//--------------------------------------------------------------------------
plugin_t PLUGIN =
{
  IDP_INTERFACE_VERSION,
  PLUGIN_MULTI,         // The plugin can work with multiple idbs in parallel
  init,                 // initialize
  nullptr,
  nullptr,
  comment,              // long comment about the plugin
  nullptr,              // multiline help about the plugin
  "Find value ranges of the register", // the preferred short name of the plugin
  nullptr,              // the preferred hotkey to run the plugin
};

<?php

class sfSagePayActions extends sfActions
{
  /**
   * Handles the page wrapper for 3D-Secure connection.
   * To access this page, the following variables MUST be available
   * in the user session:
   * - 3DAuthenticationInProgress = true
   * - MD
   * - ACSURL
   * - PAReq
   *
   * If any are missing, a 404 will be thrown.
   * This action is intended to be used with the sf_sagepay_3diframe route.
   *
   * @param sfWebRequest $request
   */
  public function execute3DSecure(sfWebRequest $request)
  {
    $this->forward404Unless($this->getUser()->getAttribute("3DAuthenticationInProgress", false));
    $this->forward404Unless(strlen($this->getUser()->getAttribute("MD", "")));
    $this->forward404Unless(strlen($this->getUser()->getAttribute("ACSURL", "")));
    $this->forward404Unless(strlen($this->getUser()->getAttribute("PAReq", "")));
  }

  /**
   * The 3D-Secure IFRAME. This handles the magic connection :-)
   * Same rules apply to the above re. user session
   *
   * @param sfWebRequest $request
   */
  public function execute3DIFRAME(sfWebRequest $request)
  {
    $this->forward404Unless($this->getUser()->getAttribute("3DAuthenticationInProgress", false));
    $this->forward404Unless(strlen($this->getUser()->getAttribute("MD", "")));
    $this->forward404Unless(strlen($this->getUser()->getAttribute("ACSURL", "")));
    $this->forward404Unless(strlen($this->getUser()->getAttribute("PAReq", "")));

    // No layout required
    $this->setLayout(false);

    // Set variables to the view
    $this->MD = $this->getUser()->getAttribute("MD");
    $this->ACSURL = $this->getUser()->getAttribute("ACSURL");
    $this->PAReq = $this->getUser()->getAttribute("PAReq");

    // and unset them from the session
    $this->getUser()->offsetUnset("3DAuthenticationInProgress");
    $this->getUser()->offsetUnset("MD");
    $this->getUser()->offsetUnset("ACSURL");
    $this->getUser()->offsetUnset("PAReq");

    // We set another flag to say that we're using 3D though
    // This is used when deciding which URL to use post-3D authentication
    $this->getUser()->setAttribute("Using3DAuth", true);
  }

  /**
   * Handles the 3D-Result.  This action is empty as this is something
   * that the application should handle specifically.
   *
   * @param sfWebRequest $request
   */
  public function execute3DResult(sfWebRequest $request)
  {
    return sfView::NONE;
  }

  /**
   * End of order page. Application should handle this specifically too.
   *
   * @param sfWebRequest $request
   */
  public function executeEndURL(sfWebRequest $request)
  {
    return sfView::NONE;
  }

  /**
   * Small page to break out of the IFRAME. Will redirect the top-most
   * page to the end of order page.
   *
   * @param sfWebRequest $request
   */
  public function executeBreakIFRAME(sfWebRequest $request)
  {
    // Empty action
  }
}
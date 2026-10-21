//------------------------------------------------------------------------
// Language toggle
//-----------------------------------------------------------------------
import ExpandToggle from '@threespot/expand-toggle';

export default class Expandables {
  constructor(selector) {
    this.els = document.querySelectorAll(selector);
    this.toggles = [];

    if (!this.els) {
      return false;
    }

    this.els.forEach((el) => {
      let toggle = new ExpandToggle(el);

      this.toggles.push(toggle);

      // Collapse all siblings if the toggle is expanded (e.g. accordions)
      if (el.hasAttribute('data-expands-collapse-siblings')) {
        toggle.on('expand', () => {
          this.collapseSiblings(toggle);
        });
      }
    });

    // Automatically expands toggle on load if toggle ID matches the URL hash
    window.addEventListener('DOMContentLoaded', () => {
      let hash = window.location.hash ? window.location.hash.split('#')[1] : '';

      this.toggles.forEach(toggle => {
        const dataExpands = toggle.el.getAttribute('data-expands');
        if (dataExpands == hash) {
          toggle.expand();
        }
      });
    });

    // Close all menus if click outside of it
    // window.addEventListener('click', (evt) => {
    //   this.toggles.forEach((toggle) => {
    //     if (targetEl.contains(evt.target)) {
    //       toggle.collapse();
    //     }
    //   });
    // });
  }

  /**
   * Collapse all toggles except for the currentToggle
   *
   * @param {ExpandToggle} currentToggle
   */
  collapseSiblings(currentToggle) {
    this.toggles.forEach(toggle => {
      if (toggle !== currentToggle) {
        toggle.collapse();
      }
    });
  }
}

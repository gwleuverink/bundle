import Alpine from "alpinejs";
import persist from '@alpinejs/persist'

Alpine.plugin(persist)

export default (() => {
  window.Alpine = Alpine;

  Alpine.start();
})();

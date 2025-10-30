export default (name: string, component: CustomElementConstructor) => {
    window.customElements.get(name) || window.customElements.define(name, component);
}
const toggleDropdown = (dropdown, menu, isOpen) => {
   dropdown.classList.toggle("open", isOpen);
   menu.style.height = isOpen ? `${menu.scrollHeight}px` : "0";
};
document.querySelectorAll(".dropdown-toggle").forEach(dropdownToggle =>{
   dropdownToggle.addEventListener("click",e =>{
      e.preventDefault();
      const dropdown = e.target.closest(".dropdown-container");
      const menu = dropdown.querySelector(".dropdown-menu");
      const isOpen = dropdown.classList.contains("open");
      toggleDropdown(dropdown,menu,!isOpen);
   })
});
document.querySelector(".siderbar-toggler").addEventListener("click", () =>{
   document.querySelector(".siderbar").classList.toggle("collapsed");
});
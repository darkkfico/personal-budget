let sections = document.querySelector("#sections");
let addSections = document.getElementById("addSection");

let sectionNumber = 2;

addSection.addEventListener("click", e => {
    e.preventDefault();
    const html = `<div class='w-full relative animate-myanimation'>
                                <span class="text-red-500 absolute top-0 left-0">*</span>
                                <input type='text' name='custom-field${sectionNumber}'
                                    placeholder='Name of section ${sectionNumber}'
                                    class='w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary font-semiboldfocus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/70 placeholder:font-semibold'>
                            </div>

                            <div class='relative inline-block w-full animate-myanimation'>
                                <input type='number' name='custom-field${sectionNumber}-amount' min='0' max='100'
                                    placeholder='Percentage of section ${sectionNumber}'
                                    class='w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/60 placeholder:font-semiboldpercentInput'>
                                <span class='pointer-events-none absolute right-10 top-1/2 -translate-y-1/2 text-[#004d40]/60'>
                                    %
                                </span>
                            </div>`;

    sections.insertAdjacentHTML("beforeend", html);
    sectionNumber++;
})
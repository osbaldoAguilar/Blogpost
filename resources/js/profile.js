import DOMPurify from "dompurify";
import axios from "axios"; // Ensure axios is imported

export default class Profile {
    constructor() {
        this.links = document.querySelectorAll(".profile-nav a");
        this.contentArea = document.querySelector(".profile-slot-content");

        // Call handleChange once on load in case of direct or refreshed visits
        this.handleChange();

        // Register events
        this.events();
    }

    // Register events
    events() {
        window.addEventListener("popstate", () => this.handleChange());
        this.links.forEach((link) => {
            link.addEventListener("click", (e) => this.handleLinkClick(e));
        });
    }

    async handleChange() {
        // Reset active classes on navigation links
        this.links.forEach((link) => link.classList.remove("active"));

        for (const link of this.links) {
            // Check if the link matches the current path
            if (link.getAttribute("href") === window.location.pathname) {
                try {
                    const response = await axios.get(link.href + "/raw");
                    this.contentArea.innerHTML = DOMPurify.sanitize(
                        response.data.theHtml
                    );
                    document.title = `${response.data.docTitle} | OurApp`;
                    link.classList.add("active");
                } catch (error) {
                    console.error("Error loading profile content:", error);
                }
                break;
            }
        }
    }

    async handleLinkClick(e) {
        e.preventDefault();

        // Mark the clicked link as active
        this.links.forEach((link) => link.classList.remove("active"));
        e.target.classList.add("active");

        try {
            // Fetch and render content
            const response = await axios.get(e.target.href + "/raw");
            this.contentArea.innerHTML = DOMPurify.sanitize(
                response.data.theHtml
            );
            document.title = `${response.data.docTitle} | OurApp`;

            // Push new state to history
            history.pushState({}, "", e.target.href);
        } catch (error) {
            console.error("Error loading profile content:", error);
        }
    }
}

// import DOMPurify from "dompurify";

// export default class Profile {
//     constructor() {
//         this.links = document.querySelectorAll(".profile-nav a");
//         this.contentArea = document.querySelector(".profile-slot-content");
//         this.events();
//     }

//     // events
//     events() {
//         addEventListener("popstate", () => {
//             this.handleChange();
//         });
//         this.links.forEach((link) => {
//             link.addEventListener("click", (e) => this.handleLinkClick(e));
//         });
//     }

//     handleChange() {
//         this.links.forEach((link) => link.classList.remove("active"));
//         this.links.forEach(async (link) => {
//             if (link.getAttribute("href") == window.location.pathname) {
//                 const response = await axios.get(link.href + "/raw");
//                 this.contentArea.innerHTML = DOMPurify.sanitize(
//                     response.data.theHTML
//                 );
//                 document.title = response.data.docTitle + " | OurApp";
//                 link.classList.add("active");
//             }
//         });
//     }

//     // methods
//     async handleLinkClick(e) {
//         this.links.forEach((link) => link.classList.remove("active"));
//         e.target.classList.add("active");
//         e.preventDefault();
//         const response = await axios.get(e.target.href + "/raw");
//         this.contentArea.innerHTML = DOMPurify.sanitize(response.data.theHTML);
//         document.title = response.data.docTitle + " | OurApp";

//         history.pushState({}, "", e.target.href);
//     }
// }

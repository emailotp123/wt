package com.example.bookstore;

import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@Controller
public class BookstoreController {
    private final UserRepository userRepo;
    private final BookRepository bookRepo;
    private final PasswordEncoder encoder;

    public BookstoreController(UserRepository u, BookRepository b, PasswordEncoder e) {
        this.userRepo = u; this.bookRepo = b; this.encoder = e;
        seedBooks(b);
    }

    private void seedBooks(BookRepository b) {
        if (b.count() == 0) {
            for (Object[] d : new Object[][]{
                {"Clean Code", "Robert C. Martin", "Programming", 599.0, "A handbook of agile software craftsmanship."},
                {"The Pragmatic Programmer", "D. Thomas", "Programming", 750.0, "Your journey to mastery."},
                {"Design Patterns", "Gang of Four", "Programming", 850.0, "Elements of reusable software."},
                {"Introduction to Algorithms", "CLRS", "Computer Science", 1200.0, "The definitive guide to algorithms."},
                {"Atomic Habits", "James Clear", "Self-Help", 399.0, "Build good habits and break bad ones."},
            }) {
                Book bk = new Book();
                bk.setTitle((String)d[0]); bk.setAuthor((String)d[1]);
                bk.setCategory((String)d[2]); bk.setPrice((Double)d[3]);
                bk.setDescription((String)d[4]);
                b.save(bk);
            }
        }
    }

    @GetMapping({"/", "/home"})
    public String home() { return "home"; }

    @GetMapping("/catalog")
    public String catalog(@RequestParam(required = false) String search,
                          @RequestParam(required = false) String category, Model model) {
        List<Book> books;
        if (search != null && !search.isEmpty()) books = bookRepo.findByTitleContainingIgnoreCase(search);
        else if (category != null && !category.isEmpty()) books = bookRepo.findByCategory(category);
        else books = bookRepo.findAll();
        model.addAttribute("books", books);
        model.addAttribute("search", search);
        model.addAttribute("category", category);
        model.addAttribute("categories", List.of("Programming", "Computer Science", "Self-Help", "Fiction", "Science"));
        return "catalog";
    }

    @GetMapping("/register")
    public String registerPage(Model model) { model.addAttribute("user", new User()); return "register"; }

    @PostMapping("/register")
    public String register(@ModelAttribute User user, Model model) {
        if (userRepo.findByEmail(user.getEmail()).isPresent()) {
            model.addAttribute("error", "Email already registered."); return "register";
        }
        user.setPassword(encoder.encode(user.getPassword()));
        userRepo.save(user);
        return "redirect:/login?registered";
    }

    @GetMapping("/login")
    public String loginPage(@RequestParam(required = false) String registered, Model model) {
        if (registered != null) model.addAttribute("success", "Registered! Please login.");
        return "login";
    }
}

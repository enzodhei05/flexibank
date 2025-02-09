@Controller
public class ProfileController {

    @Autowired
    private UserService userService;

    @GetMapping("/profile")
    public String getProfile(Model model, @AuthenticationPrincipal User user) {
        model.addAttribute("user", userService.getUserProfile(user));
        return "profile"; // le nom de ton fichier HTML (profile.html.twig)
    }

    @PostMapping("/profile/update")
    public String updateProfile(@RequestParam("profilePicture") MultipartFile file, @AuthenticationPrincipal User user) {
        // Gérer l'upload de l'image et la mise à jour des informations utilisateur
        userService.updateProfilePicture(user, file);
        return "redirect:/profile";
    }

    @PostMapping("/profile/delete")
    public String deleteAccount(@AuthenticationPrincipal User user) {
        userService.deleteAccount(user);
        return "redirect:/login"; // Redirection après la suppression
    }
}
